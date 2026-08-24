<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\AbstractReviewMail;
use App\Models\AbstractSubmission;
use App\Models\ScientificScore;
use App\Models\PresentationCategory;
use App\Models\PresentationType;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use App\Models\AbstractSubmissionReview;
use App\Models\AbstractAssignment;
use Illuminate\Support\Facades\Auth;


class AbstractReviewerController extends Controller
{
    public function score($submissionId)
    {      
        $user = \Illuminate\Support\Facades\Auth::user();
        /** @var \App\Models\User $user */

        $submission = AbstractSubmission::with('assignedUser')->findOrFail($submissionId);
        $user = Auth::user();

        $isAdmin = $user->is_admin == 1 || $user->hasAnyRole(['webadmin', 'admin']);
        $isAssignedReviewer = optional($submission->assignedUser)->assigned_to == $user->id;

        abort_unless($isAdmin || $isAssignedReviewer, 403, 'You are not assigned to review this abstract.');
        $criteria               = ScientificScore::where('status', 'active')->orderBy('id')->get();
        $presentationTypes      = PresentationType::where('status', 'active')->orderBy('id')->get();
        $presentationCategories = PresentationCategory::where('status', 'active')->orderBy('id')->get();

        $existingReview = AbstractSubmissionReview::where('abstract_submission_id', $submission->id)
            ->where('reviewed_by', $user->id)
            ->where(function ($q) {
                $q->whereNotNull('total_score')->orWhere('conflict_of_interest', true);
            })
            ->with(['scores', 'presentationType', 'presentationCategory'])
            ->first();

        return view('backend.pages.abstract-reviewer.index', compact(
            'submission', 'criteria', 'presentationTypes', 'presentationCategories', 'existingReview'
        ));
    }

    public function storeScore(Request $request, $submissionId)
    {
        $submission = AbstractSubmission::with('assignedUser')->findOrFail($submissionId);
        $user = Auth::user();
        $isAdmin = $user->is_admin == 1 || $user->hasAnyRole(['webadmin', 'admin']);
        $isAssignedReviewer = optional($submission->assignedUser)->assigned_to == $user->id;
        abort_unless($isAdmin || $isAssignedReviewer, 403);

        $alreadyReviewed = AbstractSubmissionReview::where('abstract_submission_id', $submission->id)
            ->where('reviewed_by', $user->id)
            ->where(function ($q) {
                $q->whereNotNull('total_score')->orWhere('conflict_of_interest', true);
            })
            ->exists();
        if ($alreadyReviewed) {
            return response()->json([
                'success' => false,
                'message' => 'You have already submitted your review for this abstract. Reviews cannot be edited after submission.',
            ], 422);
        }

        $validated = $request->validate([
            'conflict_of_interest'     => 'required|boolean',
            'scores'                   => 'required|array',
            'scores.*'                 => 'required|integer|min:0',
            'presentation_type_id'     => 'required|exists:presentation_types,id',
            'presentation_category_id' => 'required|exists:presentation_categories,id',
            'other_category_text'      => 'nullable|string|max:255',
        ]);
        $isConflicted = (bool) $validated['conflict_of_interest'];
        $totalScore   = null;
        DB::transaction(function () use ($validated, $submission, $user, $isConflicted, &$totalScore) {
            $review = AbstractSubmissionReview::create([
                'abstract_submission_id'   => $submission->id,
                'reviewed_by'              => $user->id,
                'conflict_of_interest'     => $isConflicted,
                'presentation_type_id'     => $validated['presentation_type_id'],
                'presentation_category_id' => $validated['presentation_category_id'],
                'other_category_text'      => $validated['other_category_text'] ?? null,
                'status'                   => 'approved',
            ]);
            if (!empty($validated['scores'])) {
                foreach ($validated['scores'] as $criterionId => $score) {
                    $score = (int) $score;
                    $review->scores()->create([
                        'scientific_score_id' => $criterionId,
                        'score'               => $score,
                    ]);
                    $totalScore += $score;
                }
                $review->update([
                    'total_score' => $totalScore,
                ]);
            }
            AbstractAssignment::where('abstract_submission_id', $submission->id)
            ->where('assigned_to', $user->id)
            ->delete();
        });
        return response()->json([
            'success'     => true,
            'message'     => 'Review submitted successfully',
            'total_score' => $totalScore,
        ]);
    } 


    public function abstractReviewerDetail(Request $request, $id)
    {
        $submission = AbstractSubmission::with([
            'reviews.reviewer',
            'reviews.scores.criterion',
            'reviews.presentationType',
            'reviews.presentationCategory',
        ])->findOrFail($id);

        $reviewsHtml = '';

        foreach ($submission->reviews as $review) {

            $reviewerName = e(optional($review->reviewer)->name ?? 'Unknown Reviewer');

            $reviewDate = optional($review->created_at)
                ? $review->created_at->format('d M Y h:i A')
                : '';

            /*
            |--------------------------------------------------------------------------
            | Review Status Badge
            |--------------------------------------------------------------------------
            */
            $badge = match ($review->status) {
                'approved' => 'success',
                'rejected' => 'danger',
                default => 'warning',
            };

            /*
            |--------------------------------------------------------------------------
            | Conflict of Interest
            |--------------------------------------------------------------------------
            */
            $conflictHtml = '';

            if ($review->conflict_of_interest) {
                $conflictHtml = '
                    <div class="mt-2">
                        <span class="badge bg-danger">
                            <i class="fa-solid fa-triangle-exclamation me-1"></i>
                            Conflict of Interest
                        </span>
                    </div>
                ';
            } else {
                $conflictHtml = '
                    <div class="mt-2">
                        <span class="badge bg-success">
                            <i class="fa-solid fa-circle-check me-1"></i>
                            No Conflict of Interest
                        </span>
                    </div>
                ';
            }

            /*
            |--------------------------------------------------------------------------
            | Presentation Type
            |--------------------------------------------------------------------------
            */
            $presentationTypeHtml = '';

            if ($review->presentationType) {
                $presentationTypeHtml = '
                    <div class="mt-2">
                        <strong>Presentation Type:</strong>
                        ' . e($review->presentationType->name) . '
                    </div>
                ';
            }

            /*
            |--------------------------------------------------------------------------
            | Presentation Category
            |--------------------------------------------------------------------------
            */
            $presentationCategoryHtml = '';

            if ($review->presentationCategory) {
                $presentationCategoryHtml = '
                    <div class="mt-2">
                        <strong>Presentation Category:</strong>
                        ' . e($review->presentationCategory->name) . '
                    </div>
                ';
            }

            /*
            |--------------------------------------------------------------------------
            | Other Category
            |--------------------------------------------------------------------------
            */
            $otherCategoryHtml = '';

            if (!empty($review->other_category_text)) {
                $otherCategoryHtml = '
                    <div class="mt-2">
                        <strong>Other Category:</strong>
                        ' . e($review->other_category_text) . '
                    </div>
                ';
            }

            /*
            |--------------------------------------------------------------------------
            | Total Score
            |--------------------------------------------------------------------------
            */
            $totalScoreHtml = '';

            if ($review->total_score !== null) {
                $totalScoreHtml = '
                    <div class="mt-3">
                        <span class="badge bg-primary fs-6">
                            <i class="fa-solid fa-star me-1"></i>
                            Total Score: ' . e($review->total_score) . '
                        </span>
                    </div>
                ';
            }

            /*
            |--------------------------------------------------------------------------
            | Individual Scores
            |--------------------------------------------------------------------------
            */
            $scoresHtml = '';

            if ($review->scores->count()) {

                $scoresHtml .= '
                    <div class="mt-3">
                        <strong>Score Details:</strong>
                        <div class="table-responsive mt-2">
                            <table class="table table-sm table-bordered mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Criterion</th>
                                        <th width="100" class="text-center">Score</th>
                                    </tr>
                                </thead>
                                <tbody>
                ';

                foreach ($review->scores as $score) {

                    $criterionName = optional($score->criterion)->name
                        ?? 'Criterion #' . $score->scientific_score_id;

                    $scoresHtml .= '
                        <tr>
                            <td>' . e($criterionName) . '</td>
                            <td class="text-center fw-bold">
                                ' . e($score->score) . '
                            </td>
                        </tr>
                    ';
                }

                $scoresHtml .= '
                                </tbody>
                            </table>
                        </div>
                    </div>
                ';
            }

            /*
            |--------------------------------------------------------------------------
            | Complete Review Card
            |--------------------------------------------------------------------------
            */
            $reviewsHtml .= '
                <div class="border rounded p-3 mb-3 bg-light">

                    <div class="d-flex justify-content-between align-items-center">

                        <div>
                            <span class="badge bg-' . $badge . '">
                                ' . ucfirst(e($review->status)) . '
                            </span>

                            <span class="ms-2 fw-semibold">
                                <i class="fa-solid fa-user me-1"></i>
                                ' . $reviewerName . '
                            </span>
                        </div>

                        <small class="text-muted">
                            ' . $reviewDate . '
                        </small>

                    </div>

                    ' . $conflictHtml . '

                    ' . $presentationTypeHtml . '

                    ' . $presentationCategoryHtml . '

                    ' . $otherCategoryHtml . '

                    ' . $totalScoreHtml . '

                    ' . $scoresHtml . '

                </div>
            ';
        }

        /*
        |--------------------------------------------------------------------------
        | No Review
        |--------------------------------------------------------------------------
        */
        if ($reviewsHtml == '') {

            $reviewsHtml = '
                <div class="alert alert-light mb-0">
                    <i class="fa-solid fa-info-circle me-1"></i>
                    No reviewer has submitted a review for this abstract yet.
                </div>
            ';
        }

        /*
        |--------------------------------------------------------------------------
        | Applicant / Abstract Details
        |--------------------------------------------------------------------------
        */
        $form = '
            <div class="modal-body">

                <div class="mb-3">
                    <h4 class="mb-1">
                        Applicant Name
                    </h4>

                    <div class="fw-semibold">
                        ' . e($submission->first_name . ' ' . $submission->last_name) . '
                    </div>
                </div>

                <div class="mb-3">
                    <h4 class="mb-1">
                        Abstract Title
                    </h4>

                    <div class="fw-semibold">
                        ' . e($submission->abstract_title) . '
                    </div>
                </div>

                <hr>

                <h6 class="fw-bold mb-3">
                    <i class="fa-solid fa-users me-1"></i>
                    Reviewer Details & Review History
                </h6>

                <div style="max-height:500px; overflow-y:auto;">
                    ' . $reviewsHtml . '
                </div>

            </div>
        ';

        return response()->json([
            'message' => 'Reviewer details loaded successfully',
            'form'    => $form,
        ]);
    } 

    
}



