<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AbstractSubmission;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class AbstractSubmissionController extends Controller
{
    public function index(Request $request)
    {
        $query = AbstractSubmission::query();
        if ($request->filled('presentation_type')) {
            $query->where(
                'presentation_type',
                $request->presentation_type
            );
        }
        $abstractSubmissions = $query
            ->latest()
            ->paginate(30);

        if ($request->ajax()) {
            return view(
                'backend.pages.abstract-submission.partials.abstract-submission-list',
                compact('abstractSubmissions')
            )->render();
        }
        return view(
            'backend.pages.abstract-submission.index',
            compact('abstractSubmissions')
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
}
