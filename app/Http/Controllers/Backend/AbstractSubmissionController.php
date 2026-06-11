<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\AbstractReviewMail;
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
        $submission = AbstractSubmission::with('reviews.reviewer')
            ->findOrFail($id);

        $existingReview = AbstractSubmissionReview::where(
            'abstract_submission_id',
            $submission->id
        )
            ->where('reviewed_by', Auth::id())
            ->first();
        $reviewsHtml = '';
        foreach ($submission->reviews as $review) {
            $badge = match ($review->status) {
                'approved' => 'success',
                'rejected' => 'danger',
                default => 'warning'
            };
            $reviewsHtml .= '
            <div class="border rounded p-3 mb-2 bg-light">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <span class="badge bg-' . $badge . '">
                            ' . ucfirst($review->status) . '
                        </span>
                        <small class="text-muted ms-2">
                            ' . optional($review->reviewer)->name . '
                        </small>
                    </div>
                    <small class="text-muted">
                        ' . $review->created_at->format('d M Y h:i A') . '
                    </small>
                </div>';
            if (!empty($review->comment)) {
                $reviewsHtml .= '
                <div class="mt-2">
                    ' . e($review->comment) . '
                </div>';
            }
            $reviewsHtml .= '
            </div>';
        }
        if ($reviewsHtml == '') {
            $reviewsHtml = '
            <div class="alert alert-light mb-0">
                No review history found.
            </div>';
        }
        $form = '<div class="modal-body">';
        if ($submission->status == 'approved') {
            $form .= '
            <div class="alert alert-success">
                <i class="fa-solid fa-circle-check me-2"></i>
                This abstract has already been approved.
                Further modifications are disabled.
            </div>
            <div class="mb-3">
                <h3>Applicant Name : ' . $submission->first_name . ' ' . $submission->last_name . '</h3>
            </div>
            <div class="mb-3">
                <h4> Abstract Title : ' .$submission->abstract_title. '</h4>
            </div>
            <hr>
            <h6 class="fw-bold mb-3">
                <i class="fa-solid fa-comments me-1"></i>
                Review History
            </h6>
            <div style="max-height:250px;overflow-y:auto;">
                ' . $reviewsHtml . '
            </div>';
        } else {
            $action = $existingReview
                ? route('abstract-review.update', $existingReview->id)
                : route('abstract-review.store');
            $buttonText = $existingReview
                ? 'Update Review'
                : 'Save Review';
            $statusValue = $existingReview->status ?? $submission->status;
            $commentValue = $existingReview->comment ?? '';
            $form .= '
            <form action="' . $action . '"
                method="POST"
                id="abstractReviewForm">
                ' . csrf_field() . '
                ' . ($existingReview ? method_field('PUT') : '') . '
                <input type="hidden"
                    name="id"
                    value="' . $submission->id . '">
                <div class="mb-3">
                    <label class="form-label">Applicant Name</label>
                    <input type="text"
                        class="form-control"
                        value="' . $submission->first_name . ' ' . $submission->last_name . '"
                        disabled>
                </div>
                <div class="mb-3">
                    <label class="form-label">Update Status *</label>
                    <select name="status"
                        id="status"
                        class="form-select">
                        <option value="pending"
                            ' . ($statusValue == 'pending' ? 'selected' : '') . '>
                            Pending
                        </option>
                        <option value="approved"
                            ' . ($statusValue == 'approved' ? 'selected' : '') . '>
                            Approved
                        </option>
                        <option value="rejected"
                            ' . ($statusValue == 'rejected' ? 'selected' : '') . '>
                            Rejected
                        </option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Comment *</label>
                    <textarea name="comment"
                        id="comment"
                        rows="4"
                        class="form-control"
                        placeholder="Write review comment...">' . $commentValue . '</textarea>
                </div>
                <div class="modal-footer px-0 pb-0">
                    <button type="button"
                        class="btn btn-secondary"
                        data-bs-dismiss="modal">
                        Close
                    </button>
                    <button type="submit"
                        id="abstractReviewSave"
                        class="btn btn-primary">
                        ' . $buttonText . '
                    </button>
                </div>
            </form>
            <hr>
            <h6 class="fw-bold mb-3">
                <i class="fa-solid fa-comments me-1"></i>
                Review History
            </h6>
            <div style="max-height:250px;overflow-y:auto;">
                ' . $reviewsHtml . '
            </div>';
        }
        $form .= '</div>';
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
        $abstract_submission_email = $submission->email;

        $submission->update([
            'status' => $request->status
        ]);
        AbstractSubmissionReview::create([
            'abstract_submission_id' => $submission->id,
            'reviewed_by' => Auth::id(),
            'status' => $request->status,
            'comment' => $request->comment
        ]);
        $html = view(
            'backend.pages.abstract-submission.partials.abstract-submission-list',
            [
                'abstractSubmissions' => AbstractSubmission::latest()->paginate(10)
            ]
        )->render();
        if (!empty($abstract_submission_email)) {
            Mail::to(trim($abstract_submission_email))
                ->queue(
                    (new AbstractReviewMail(
                        $submission,
                        $request->comment
                    ))
                        ->replyTo(
                            config('mail.from.address'),
                            config('mail.from.name')
                        )
                );
        }
        return response()->json([
            'status' => 'success',
            'message' => 'Review saved successfully.',
            'html' => $html
        ]);
    }

    public function abstractReviewUpdate(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:pending,approved,rejected',
            'comment' => 'required|string'
        ]);
        $review = AbstractSubmissionReview::findOrFail($id);
        if ($review->reviewed_by != Auth::id()) {
            abort(403);
        }
        $review->update([
            'status' => $request->status,
            'comment' => $request->comment
        ]);
        $review->abstract->update([
            'status' => $request->status
        ]);
        $html = view(
            'backend.pages.abstract-submission.partials.abstract-submission-list',
            [
                'abstractSubmissions' => AbstractSubmission::latest()->paginate(10)
            ]
        )->render();
        return response()->json([
            'status' => 'success',
            'message' => 'Review updated successfully.',
            'html' => $html
        ]);
    }
}
