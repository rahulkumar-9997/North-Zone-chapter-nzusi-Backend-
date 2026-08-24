<div class="border rounded p-3 mb-3 bg-light">
    <div class="row g-2">
        <div class="col-md-6"><strong>Abstract ID:</strong> {{ $submission->abstract_id ?? '—' }}</div>
        <div class="col-md-6"><strong>Reviewer:</strong> {{ auth()->user()->name }}</div>
    </div>
</div>

<form id="review-score-form"
    data-route="{{ route('abstract-review.score.store', $submission->id) }}"
    data-redirect="{{ route('abstract-submission.index') }}"
    novalidate>
    @csrf

    <div class="row">
        <div class="col-sm-6 col-12">
            <div class="mb-3">
                <label class="form-label">Abstract ID<span class="text-danger ms-1">*</span></label>
                <input type="text" class="form-control" name="abstract_id" value="{{ $submission->abstract_id ?? '—' }}" readonly>
            </div>
        </div>
        <div class="col-sm-6 col-12">
            <div class="mb-3">
                <label class="form-label">Reviewer ID<span class="text-danger ms-1">*</span></label>
                <input type="text" class="form-control" name="reviewer_id" value="{{ auth()->user()->id }}" readonly>
            </div>
        </div>
    </div>

    <div class="mb-3">
        <label class="form-label fw-semibold d-block">Conflict of Interest?</label>
        <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" name="conflict_of_interest" id="coi_no" value="0" checked>
            <label class="form-check-label" for="coi_no">No</label>
        </div>
        <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" name="conflict_of_interest" id="coi_yes" value="1">
            <label class="form-check-label" for="coi_yes">Yes</label>
        </div>        
    </div>

    <div id="scoring-section">
        <h5 class="fw-bold mb-2">A. Scientific Score</h5>
        <div class="table-responsive mb-3">
            <table class="table table-sm table-bordered align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Criterion</th>
                        <th class="text-center" style="width:140px;">Score</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($criteria as $criterion)
                    <tr>
                        <td>{{ $criterion->criterion }}</td>
                        <td>
                            <div class="input-group input-group-sm">
                                <input type="number" class="form-control criterion-score text-center"
                                    name="scores[{{ $criterion->id }}]" min="0" max="{{ $criterion->score }}"
                                    data-max="{{ $criterion->score }}" value="0">
                                <span class="input-group-text">/ {{ $criterion->score }}</span>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <td><strong>TOTAL</strong></td>
                        <td class="text-center"><strong><span id="total-score">0</span> / {{ $criteria->sum('score') }}</strong></td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <h5 class="fw-bold mb-2">
            B. Presentation Type
            <small class="text-muted fw-normal">(select one)</small>
        </h5>
        <div class="mb-1" id="presentation-type-group">
            @foreach($presentationTypes as $type)
            <div class="form-check">
                <input class="form-check-input" type="radio" name="presentation_type_id" id="ptype_{{ $type->id }}" value="{{ $type->id }}">
                <label class="form-check-label" for="ptype_{{ $type->id }}">{{ $type->name }}</label>
            </div>
            @endforeach
        </div>
        <div class="invalid-feedback mb-3" id="presentation-type-error">Please select one presentation type.</div>

        <h5 class="fw-bold mb-2">Presentation Category <small class="text-muted fw-normal">(select one primary category)</small></h5>
        <select class="form-select mb-1" name="presentation_category_id" id="category-select">
            <option value="">-- Select Category --</option>
            @foreach($presentationCategories as $category)
            <option value="{{ $category->id }}">{{ $category->name }}</option>
            @endforeach
        </select>
        <div class="invalid-feedback" id="category-error">Please select a presentation category.</div>

        <input type="text" class="form-control mb-3 mt-2 d-none" id="other-category-text" name="other_category_text" placeholder="Please specify category">
        <div class="invalid-feedback" id="other-category-error">Please specify the category.</div>
    </div>

    <div class="alert alert-secondary py-2 small mb-3 mt-3">
        <i class="fa-solid fa-circle-info me-1"></i>
        Please ensure your abstract review is final before submitting. Once submitted, it cannot be edited or modified.
    </div>

    <div class="mt-3 mb-3">
        <h6 class="text-danger">
            If an abstract fits more than one category, select the single category that best represents its primary subject.
        </h6>
    </div>

    <button type="submit" class="btn btn-primary"><i class="fa-solid fa-paper-plane me-1"></i> Submit Review</button>
</form>