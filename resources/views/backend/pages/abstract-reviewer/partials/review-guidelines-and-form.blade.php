<div id="review-guidelines-block">
    @include('backend.pages.abstract-reviewer.partials.review-guidelines-content')
    <div class="border-top pt-3 text-end">
        <button type="button"
            id="guidelines-continue-btn"
            class="btn btn-success">
            <i class="bi bi-check-circle-fill me-1"></i>
            I Have Read the Guidelines — Continue to Review Form
        </button>
    </div>
</div>
<div id="review-score-form-block" class="d-none">
    @include(
    'backend.pages.abstract-reviewer.partials.review-score-form',
    compact(
    'submission',
    'criteria',
    'presentationTypes',
    'presentationCategories'
    )
    )

</div>