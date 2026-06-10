<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AbstractSubmission;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use App\Models\AbstractSubmissionReview;
use Illuminate\Support\Facades\Auth;

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

        if ($request->filled('topic_category')) {
            $query->where(
                'topic_category',
                $request->topic_category
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

    public function abstractReviewForm(Request $request, $id)
    {
        $submission = AbstractSubmission::with('reviews')->findOrFail($id);
        $canEdit = ($submission->post_user == Auth::id());
        $existingReview = AbstractSubmissionReview::where('abstract_submission_id', $id)
        ->where('reviewed_by',  Auth::id())->first();
        //$canEdit = ($submission->post_user == Auth::id());
        $reviewsHtml = '';
        foreach ($submission->reviews as $review) {
            $badge = match($review->status) {
                'approved' => 'success',
                'rejected' => 'danger',
                default => 'warning'
            };
            $reviewsHtml .= '
                <div class="border rounded p-2 mb-2 bg-light">
                    <div class="d-flex justify-content-between">
                        <span class="badge bg-'.$badge.'">'.ucfirst($review->status).'</span>
                        <small>'.$review->created_at->format("d M Y h:i A").'</small>
                    </div>';
                    if($review->comment){
                        $reviewsHtml .= '
                        <div class="mt-2">
                            '.$review->comment.'
                        </div>';
                    }
                $reviewsHtml .= '
                </div>
            ';
        }
        if ($reviewsHtml == '') {
            $reviewsHtml = '<p class="text-muted">No reviews yet</p>';
        }
        $form = '
        <div class="modal-body">';
            if($existingReview){
                $form .= '
                <form action="'.route('abstract-review.update', $existingReview->id).'"
                    method="POST"
                    id="abstractReviewForm">
                    '.csrf_field().'
                    <input type="hidden" name="id" value="'.$submission->id.'">
                    <div class="mb-3">
                        <label class="form-label">Applicant Name</label>
                        <input type="text" class="form-control" value="'.$submission->first_name.' '.$submission->last_name.'" disabled>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Update Status *</label>
                        <select name="status" class="form-select" id="status">
                            <option value="pending" '.($submission->status=='pending'?'selected':'').'>Pending</option>
                            <option value="approved" '.($submission->status=='approved'?'selected':'').'>Approved</option>
                            <option value="rejected" '.($submission->status=='rejected'?'selected':'').'>Rejected</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Comment</label>
                        <textarea name="comment"
                        class="form-control" id="comment"
                        rows="4"
                        placeholder="Write review comment..."></textarea>
                    </div>
                    <div class="modal-footer px-0 pb-0">
                        <button type="button"
                        class="btn btn-secondary"
                        data-bs-dismiss="modal">
                        Close
                        </button>
                        <button type="submit" id="abstractReviewSave"
                        class="btn btn-primary">
                            Save Review
                        </button>
                    </div>
                </form>';
            }
            else{
                $form .= '
                <form action="'.route('abstract-review.store').'"
                    method="POST"
                    id="abstractReviewForm">
                    '.csrf_field().'
                    <input type="hidden" name="id" value="'.$submission->id.'">
                    <div class="mb-3">
                        <label class="form-label">Applicant Name</label>
                        <input type="text" class="form-control" value="'.$submission->first_name.' '.$submission->last_name.'" disabled>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Update Status *</label>
                        <select name="status" class="form-select" id="status">
                            <option value="pending" '.($submission->status=='pending'?'selected':'').'>Pending</option>
                            <option value="approved" '.($submission->status=='approved'?'selected':'').'>Approved</option>
                            <option value="rejected" '.($submission->status=='rejected'?'selected':'').'>Rejected</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Comment</label>
                        <textarea name="comment"
                        class="form-control" id="comment"
                        rows="4"
                        placeholder="Write review comment..."></textarea>
                    </div>
                    <div class="modal-footer px-0 pb-0">
                        <button type="button"
                        class="btn btn-secondary"
                        data-bs-dismiss="modal">
                        Close
                        </button>
                        <button type="submit" id="abstractReviewSave"
                        class="btn btn-primary">
                            Save Review
                        </button>
                    </div>
                </form>';
            }
            $form .= '
            <div class="mt-2" style="max-height:250px; overflow-y:auto;">
                '.$reviewsHtml.'
            </div>
        </div>
        ';
        return response()->json([
            'message' => 'Form loaded successfully',
            'form' => $form,
        ]);
    }

    public function abstractReviewFormSubmit(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:abstract_submissions,id',
            'status' => 'required|in:pending,approved,rejected',
            'comment' => 'required|string'
        ]);
        $submission = AbstractSubmission::findOrFail($request->id);
        $submission->update([
            'status' => $request->status
        ]);
        AbstractSubmissionReview::create([
            'abstract_submission_id' => $submission->id,
            'reviewed_by' => Auth::id(),
            'status' => $request->status,
            'comment' => $request->comment
        ]);
        $html = view('backend.pages.abstract-submission.partials.abstract-submission-list', [
            'abstractSubmissions' => AbstractSubmission::latest()->paginate(10)
        ])->render();

        return response()->json([
            'status' => 'success',
            'message' => 'Abstract review saved successfully',
            'html' => $html
        ]);
    }

}
