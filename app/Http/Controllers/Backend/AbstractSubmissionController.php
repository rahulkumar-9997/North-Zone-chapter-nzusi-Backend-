<?php

namespace App\Http\Controllers\Backend;

use App\Exports\AbstractSubmissionsExport;
use App\Http\Controllers\Controller;
use App\Models\AbstractAssignment;
use App\Models\AbstractSubmission;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class AbstractSubmissionController extends Controller
{    
    public function index(Request $request)
    {
        /** @var \App\Models\User $user */
        $user    = Auth::user();
        $isAdmin = $this->isAdmin($user);

        $abstractSubmissions = $this->buildListQuery($request, $user, $isAdmin)
            ->latest()
            ->paginate(30)
            ->withQueryString();
        $reviewers = $this->reviewersList();
        if ($request->ajax()) {
            return view(
                'backend.pages.abstract-submission.partials.abstract-submission-list',
                compact('abstractSubmissions', 'reviewers', 'isAdmin')
            )->render();
        }
        return view(
            'backend.pages.abstract-submission.index',
            compact('abstractSubmissions', 'reviewers', 'isAdmin')
        );
    }
    
    public function show($id)
    {
        $abstractSubmission = AbstractSubmission::findOrFail($id);

        return view('backend.pages.abstract-submission.show', compact('abstractSubmission'));
    }
    
    public function destroy($id)
    {
        abort_unless($this->isAdmin(Auth::user()), 403);
        DB::beginTransaction();
        try {
            $abstractSubmission = AbstractSubmission::findOrFail($id);
            /* Delete Supporting File */
            if (!empty($abstractSubmission->supporting_file)) {
                $filePath = 'images/abstract-submission/' . $abstractSubmission->supporting_file;
                if (Storage::disk('public')->exists($filePath)) {
                    Storage::disk('public')->delete($filePath);
                }
            }
            /* Delete Record */
            $abstractSubmission->delete();
            DB::commit();
            return redirect()
                ->route('abstract-submission.index')
                ->with('success', 'Abstract submission deleted successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Abstract Submission Delete Error: ' . $e->getMessage());
            return back()->with('error', 'Something went wrong while deleting.');
        }
    }
    
    public function assignReviewer(Request $request, AbstractSubmission $abstract)
    {
        /** @var \App\Models\User $user */
        $user    = Auth::user();
        $isAdmin = $this->isAdmin($user);
        abort_unless($isAdmin, 403, 'Only admin can assign reviewers.');
        $request->validate([
            'reviewer_id'   => 'nullable|array|max:5',
            'reviewer_id.*' => 'integer|exists:users,id',
        ], [
            'reviewer_id.max' => 'You can assign a maximum of 5 reviewers per abstract.',
        ]);
        $selectedIds = collect($request->input('reviewer_id', []))
            ->map(fn($id) => (int) $id)
            ->unique();
        $reviewedIds = $abstract->reviews()
            ->completed()
            ->pluck('reviewed_by')
            ->map(fn($id) => (int) $id);
        $selectedIds = $selectedIds->diff($reviewedIds)->values();
        DB::transaction(function () use ($abstract, $selectedIds, $user) {
            AbstractAssignment::where('abstract_submission_id', $abstract->id)
                ->whereNotIn('assigned_to', $selectedIds->all())
                ->delete();
            $alreadyAssigned = AbstractAssignment::where('abstract_submission_id', $abstract->id)
                ->pluck('assigned_to')
                ->map(fn($id) => (int) $id);
            foreach ($selectedIds->diff($alreadyAssigned) as $reviewerId) {
                AbstractAssignment::create([
                    'abstract_submission_id' => $abstract->id,
                    'assigned_to'            => $reviewerId,
                    'assigned_by'            => $user->id,
                    'assigned_at'            => now(),
                    'status'                 => 'pending',
                ]);
            }
        });
        $abstractSubmissions = $this->buildListQuery($request, $user, $isAdmin)
            ->latest()
            ->paginate(30)
            ->withPath(route('abstract-submission.index'));
        $reviewers = $this->reviewersList();
        $html = view(
            'backend.pages.abstract-submission.partials.abstract-submission-list',
            compact('abstractSubmissions', 'reviewers', 'isAdmin')
        )->render();
        return response()->json([
            'status'  => 'success',
            'html'    => $html,
            'message' => $selectedIds->isNotEmpty()
                ? 'Reviewer(s) assigned successfully.'
                : 'Reviewer(s) unassigned successfully.',
        ]);
    }

    public function export(Request $request)
    {
        abort_unless($this->isAdmin(Auth::user()), 403);
        $filters = $request->only(['presentation_type', 'topic_category', 'date_from', 'date_to']);

        return Excel::download(
            new AbstractSubmissionsExport($filters),
            'abstract-submissions-' . now()->format('Y-m-d_His') . '.xlsx'
        );
    }
    
    private function isAdmin($user): bool
    {
        /** @var \App\Models\User $user */
        return $user && ($user->is_admin == 1 || $user->hasAnyRole(['webadmin', 'admin']));
    }

    private function buildListQuery(Request $request, $user, bool $isAdmin)
    {
        $query = AbstractSubmission::query();
        if ($request->filled('presentation_type')) {
            $query->where('presentation_type', $request->presentation_type);
        }

        if ($request->filled('topic_category')) {
            $query->where('topic_category', $request->topic_category);
        }

        if ($request->filled('name')) {
            $name = trim($request->name);
            $query->where(function ($q) use ($name) {
                $q->where('first_name', 'like', "%{$name}%")
                    ->orWhere('last_name', 'like', "%{$name}%")
                    ->orWhereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%{$name}%"]);
            });
        }

        if ($request->filled('date_from')) {
            $query->whereDate('submitted_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('submitted_at', '<=', $request->date_to);
        }
        // if (!$isAdmin) {
        //     $query->visibleToReviewer($user->id);
        // }
        if (!$isAdmin) {
            $query->assignedTo($user->id);
        }
        return $query->with([
            'assignments.assignedUser:id,name',
            'reviews' => function ($q) use ($user, $isAdmin) {
                if ($isAdmin) {
                    $q->completed();
                } else {
                    $q->completedBy($user->id);
                }
                $q->with('reviewer:id,name')->latest();
            },
        ]);
    }

    private function reviewersList()
    {
        return User::whereHas('roles', function ($q) {
            $q->where('slug', 'abstract-reviewer');
        })
        ->select('id', 'name')
        ->orderBy('name')
        ->get();
    }
}
