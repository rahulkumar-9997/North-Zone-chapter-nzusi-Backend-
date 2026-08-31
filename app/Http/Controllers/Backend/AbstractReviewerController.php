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
        if ($isAdmin) {
            $submission->load([
                'reviews.reviewer',
                'reviews.scores.criterion',
                'reviews.presentationType',
                'reviews.presentationCategory',
            ]);
        }

        $criteria               = ScientificScore::where('status', 'active')->orderBy('id')->get();
        $presentationTypes      = PresentationType::where('status', 'active')->orderBy('id')->get();
        $presentationCategories = PresentationCategory::where('status', 'active')->orderBy('id')->get();

        $existingReview = AbstractSubmissionReview::where('abstract_submission_id', $submission->id)
            ->where('reviewed_by', $user->id)
            ->where(function ($q) {
                $q->whereNotNull('total_score')->orWhere('conflict_of_interest', true);
            })
            ->with(['scores.criterion', 'presentationType', 'presentationCategory', 'reviewer'])
            ->first();

        return view('backend.pages.abstract-reviewer.index', compact(
            'submission', 'criteria', 'presentationTypes', 'presentationCategories', 'existingReview', 'isAdmin'
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
            // AbstractSubmission status update
            $submission->update([
                'status' => 'approved',
            ]);

            AbstractAssignment::where('abstract_submission_id', $submission->id)
            ->where('assigned_to', $user->id)
            ->delete();
            if (!empty($submission->email)) {
                Mail::to($submission->email)
                    ->queue(
                        new AbstractReviewMail(
                            $submission,
                            'Your abstract has been reviewed by our scientific committee.'
                        )
                    );
            }
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

        $form = view('backend.pages.abstract-reviewer.partials.review-item', [
            'mode'       => 'list',
            'submission' => $submission,
        ])->render();

        return response()->json([
            'message' => 'Reviewer details loaded successfully',
            'form'    => $form,
        ]);
    }

    public function guidelines()
    {
        $criteria = ScientificScore::where('status', 'active')->orderBy('id')->get();
        return view('backend.pages.abstract-reviewer.guidelines', compact('criteria'));
    }

    
}



