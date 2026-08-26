<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\AbstractReviewMail;
use App\Models\AbstractSubmission;
use App\Models\AbstractAssignment;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use App\Models\AbstractSubmissionReview;
use Illuminate\Support\Facades\Auth;

class AbstractSubmissionController extends Controller
{
    public function index(Request $request)
    {
        $user = \Illuminate\Support\Facades\Auth::user();
        /** @var \App\Models\User $user */
        $isAdmin = $user->is_admin == 1 || $user->hasAnyRole(['webadmin', 'admin']);
        $query = AbstractSubmission::query();
        if ($request->filled('presentation_type')) {
            $query->where(
                'presentation_type',
                $request->presentation_type
            );
        }

        if ($request->filled('topic_category')) {
            $query->where(
                'topic_category',
                $request->topic_category
            );
        }
        if (!($user->is_admin == 1 || $user->hasAnyRole(['webadmin', 'admin']))) {
            $query->assignedTo($user->id);
        }

        $reviewers = User::whereHas('roles', function ($q) {
            $q->where('slug', 'abstract-reviewer');
        })->select('id', 'name')->get();

        $abstractSubmissions = $query->with([
            'assignedUser.assignedUser',
            'reviews' => function ($q) use ($user, $isAdmin) {
                if ($isAdmin) {
                    $q->completed();
                } else {
                    $q->completedBy($user->id);
                }
            },
        ])
        ->latest()
        ->paginate(30);      
        
        if ($request->ajax()) {
            return view(
                'backend.pages.abstract-submission.partials.abstract-submission-list',
                compact('abstractSubmissions', 'reviewers')
            )->render();
        }
        return view(
            'backend.pages.abstract-submission.index',
            compact('abstractSubmissions', 'reviewers')
        );
    }
    

    public function show($id)
    {
        $abstractSubmission = AbstractSubmission::findOrFail($id);
        return view('backend.pages.abstract-submission.show', compact('abstractSubmission'));
    }

    public function destroy($id)
    {
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
            return redirect()->route('abstract-submission.index')->with('success', 'Abstract submission deleted successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error(
                'Abstract Submission Delete Error: ' . $e->getMessage()
            );
            return back()->with(
                'error',
                'Something went wrong while deleting.'
            );
        }
    }   
    

    public function assignReviewer(Request $request, AbstractSubmission $abstract)
    {
        $request->validate([
            'reviewer_id' => 'nullable|exists:users,id',
        ]);
        AbstractAssignment::where('abstract_submission_id', $abstract->id)->delete();        
        if ($request->filled('reviewer_id')) {
            AbstractAssignment::create([
                'abstract_submission_id' => $abstract->id,
                'assigned_to' => $request->reviewer_id,
                'assigned_by' => Auth::id(),
                'assigned_at' => now(),
                'status' =>'completed'
            ]);
        }
        $user = \Illuminate\Support\Facades\Auth::user();
        /** @var \App\Models\User $user */        
        $query = AbstractSubmission::query();
        if (!($user->is_admin == 1 || $user->hasAnyRole(['webadmin', 'admin']))) {
            $query->assignedTo($user->id);
        }

        $reviewers = User::whereHas('roles', function ($q) {
            $q->where('slug', 'abstract-reviewer');
        })->select('id', 'name')->get();

        $abstractSubmissions = $query->with('assignedUser.assignedUser')
            ->latest()
            ->paginate(30);

        $html = view(
            'backend.pages.abstract-submission.partials.abstract-submission-list',
            compact('abstractSubmissions', 'reviewers')
        )->render();

        return response()->json([
            'status' => 'success',
            'html' => $html,
            'message' => $request->filled('reviewer_id')
                ? 'Reviewer assigned successfully.'
                : 'Reviewer unassigned successfully.',
        ]);
    }
}
