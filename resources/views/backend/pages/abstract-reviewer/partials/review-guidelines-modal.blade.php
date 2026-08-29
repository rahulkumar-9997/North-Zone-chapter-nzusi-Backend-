<div class="modal fade" id="reviewGuidelinesModal" tabindex="-1" aria-labelledby="reviewGuidelinesModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="reviewGuidelinesModalLabel">Review Guidelines</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                @include('backend.pages.abstract-reviewer.partials.review-guidelines-content', ['criteria' => $criteria])
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Got it</button>
            </div>
        </div>
    </div>
</div>