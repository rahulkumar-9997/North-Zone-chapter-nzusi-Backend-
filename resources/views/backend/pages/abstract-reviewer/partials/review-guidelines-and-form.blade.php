<div id="review-guidelines-block">
    <div class="alert alert-info mb-4">
        <i class="bi bi-clipboard-check me-2"></i>
        <strong>Abstract Review Guidelines & Objective Scoring System</strong>
    </div>
    <div class="mb-2">
        <h5 class="fw-bold mb-2">
            <i class="bi bi-lightbulb text-warning me-2"></i>
            Guiding Principle
        </h5>
        <div class="bg-light border-start border-4 border-primary rounded p-2">
            <h5 class="fw-bold mb-2">
                “Score the science, not the scientist.”
            </h5>
            <p class="mb-0 text-muted">
                Author name, institution, designation and reputation should
                never be used as selection criteria.
            </p>
        </div>
    </div>
    <div class="mb-4">
        <h6 class="fw-bold mb-3">
            <span class="badge bg-primary me-2">1</span>
            Reviewer Instructions
        </h6>
        <ul class="list-group list-group-flush border rounded">
            <li class="list-group-item py-2">
                <i class="bi bi-check-circle-fill text-success me-2"></i>
                Review abstracts
                <strong>blinded to author identity, institution and reputation</strong>.
            </li>
            <li class="list-group-item py-2">
                <i class="bi bi-check-circle-fill text-success me-2"></i>
                Declare any <strong>conflict of interest</strong>
                and do not score conflicted abstracts.
            </li>
            <li class="list-group-item py-2">
                <i class="bi bi-check-circle-fill text-success me-2"></i>
                Score the <strong>scientific merit of the work</strong>,
                not the seniority or reputation of the authors.
            </li>

            <li class="list-group-item py-2">
                <i class="bi bi-check-circle-fill text-success me-2"></i>
                Base scores only on information provided in the abstract.
            </li>

            <li class="list-group-item py-2">
                <i class="bi bi-check-circle-fill text-success me-2"></i>
                Do not penalize well-designed studies for negative results.
            </li>

            <li class="list-group-item py-2">
                <i class="bi bi-check-circle-fill text-success me-2"></i>
                Give particular importance to
                <strong>methodological rigor and actual results</strong>.
            </li>

            <li class="list-group-item py-2">
                <i class="bi bi-check-circle-fill text-success me-2"></i>
                Conclusions should be supported by the reported data.
            </li>

            <li class="list-group-item py-2">
                <i class="bi bi-check-circle-fill text-success me-2"></i>
                Flag suspected plagiarism, duplicate submission, ethical concerns
                or inconsistent/fabricated data for Scientific Committee review.
            </li>
        </ul>
    </div>
    <div class="mb-4">
        <h6 class="fw-bold mb-3">
            <span class="badge bg-primary me-2">2</span>
            Objective Scoring System
            <small class="text-muted fw-normal">(100 Points)</small>
        </h6>
        <div class="table-responsive">
            <table class="table table-sm table-bordered align-middle mb-2">
                <thead class="table-light">
                    <tr>
                        <th>Criterion</th>
                        <th class="text-center" style="width: 120px;">
                            Max Score
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($criteria as $criterion)
                    <tr>
                        <td>{{ $criterion->criterion }}</td>
                        <td class="text-center fw-semibold">
                            {{ $criterion->score }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>

                <tfoot>
                    <tr class="fw-bold table-light">
                        <td>TOTAL</td>
                        <td class="text-center">
                            {{ $criteria->sum('score') }}
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
        <div class="alert alert-light border mb-0 py-2">
            <strong>
                <i class="bi bi-bar-chart-line me-1"></i>
                Scoring Principle:
            </strong>
            <ul class="list-unstyled mb-0 mt-2">
                <li class="mb-1">
                    <i class="bi bi-check-circle-fill text-success me-2"></i>
                    <span class="text-success fw-semibold">Excellent:</span>
                    13–15 / 18–20
                </li>
                <li class="mb-1">
                    <i class="bi bi-check-circle-fill text-primary me-2"></i>
                    <span class="text-primary fw-semibold">Good:</span>
                    10–12 / 15–17
                </li>
                <li class="mb-1">
                    <i class="bi bi-dash-circle-fill text-warning me-2"></i>
                    <span class="text-warning fw-semibold">Moderate:</span>
                    7–9 / 11–14
                </li>
                <li class="mb-1">
                    <i class="bi bi-dash-circle text-secondary me-2"></i>
                    <span class="text-secondary fw-semibold">Weak:</span>
                    4–6 / 6–10
                </li>
                <li class="mb-1">
                    <i class="bi bi-x-circle-fill text-danger me-2"></i>
                    <span class="text-danger fw-semibold">Poor:</span>
                    0–3 / 0–5
                </li>
                <li class="mt-2">
                    <i class="bi bi-info-circle-fill text-info me-2"></i>
                    For studies without results,
                    <strong>
                        Results &amp; Statistical Rigor should normally not exceed 5/20.
                    </strong>
                </li>
            </ul>
        </div>
    </div>
    <div class="mb-4">
        <h6 class="fw-bold mb-3">
            <span class="badge bg-primary me-2">3</span>
            Presentation Type
        </h6>
        <p class="mb-3">
            Based on the scientific score and quality of the submission,
            the reviewer should recommend
            <strong>one primary presentation type</strong>:
        </p>
        <ul class="list-group list-group-flush border rounded">
            <li class="list-group-item py-2">
                <div class="fw-bold mb-2">
                    <i class="bi bi-camera-video-fill text-primary me-2"></i>
                    A. Best Video – Robotic
                </div>
                <div class="text-muted mb-2">
                    For exceptional robotic surgical videos demonstrating:
                </div>
                <ul class="mb-0 ps-4">
                    <li>Significant innovation</li>
                    <li>Excellent technical execution</li>
                    <li>Clear educational value</li>
                    <li>Meaningful clinical outcomes</li>
                </ul>
            </li>
            <li class="list-group-item py-2">
                <div class="fw-bold mb-2">
                    <i class="bi bi-camera-reels-fill text-primary me-2"></i>
                    B. Best Video – Non-Robotic
                </div>
                <div class="text-muted mb-2">
                    For exceptional videos involving:
                </div>
                <ul class="mb-0 ps-4">
                    <li>Endourology</li>
                    <li>Laparoscopy</li>
                    <li>Open surgery</li>
                    <li>Reconstructive surgery</li>
                    <li>Andrology</li>
                    <li>Other non-robotic procedures</li>
                </ul>
            </li>
            <li class="list-group-item py-2">
                <div class="fw-bold mb-2">
                    <i class="bi bi-award-fill text-warning me-2"></i>
                    C. Best Podium
                </div>
                <div>
                    For the <strong>highest-quality original research</strong>
                    with strong methodology, results, originality and clinical impact.
                </div>
            </li>
            <li class="list-group-item py-2">
                <div class="fw-bold mb-2">
                    <i class="bi bi-mic-fill text-primary me-2"></i>
                    D. Podium
                </div>
                <div>
                    For scientifically sound abstracts suitable for oral presentation.
                </div>
            </li>
            <li class="list-group-item py-2">
                <div class="fw-bold mb-2">
                    <i class="bi bi-file-earmark-text-fill text-success me-2"></i>
                    E. Moderated Poster
                </div>
                <div>
                    For good-quality abstracts with sufficient scientific merit
                    for presentation and discussion in a moderated poster session.
                </div>
            </li>
            <li class="list-group-item py-2">
                <div class="fw-bold mb-2">
                    <i class="bi bi-file-earmark-fill text-secondary me-2"></i>
                    F. Unmoderated Poster
                </div>
                <div>
                    For scientifically acceptable abstracts appropriate for poster
                    display but not selected for oral/moderated presentation.
                </div>
            </li>
        </ul>
    </div>
    <div class="mb-4">
        <h6 class="fw-bold mb-3">
            <span class="badge bg-primary me-2">4</span>
            Presentation Category
        </h6>
        <p class="mb-3">
            The reviewer should also identify the
            <strong>primary scientific/clinical category</strong>
            of the abstract.
        </p>
        <div class="border rounded p-3 bg-light">
            <p class="fw-semibold mb-2">
                <i class="bi bi-tags-fill text-primary me-2"></i>
                Suggested categories:
            </p>
            <ul class="mb-0 ps-4">
                <li>Uro-oncology</li>
                <li>Endourology / Stone Disease</li>
                <li>BPH / LUTS</li>
                <li>Urodynamics / Female Urology</li>
                <li>Reconstructive Urology</li>
                <li>Andrology / Sexual Medicine</li>
                <li>Pediatric Urology</li>
                <li>Renal Transplantation</li>
                <li>Robotic Urology</li>
                <li>Laparoscopy / Minimally Invasive Surgery</li>
                <li>Urological Trauma</li>
                <li>Infection / Inflammation</li>
                <li>Functional Urology</li>
                <li>Basic / Translational Research</li>
                <li>Artificial Intelligence / Digital Health</li>
                <li>
                    Other:
                    <span class="d-inline-block border-bottom"
                        style="min-width: 200px;">
                    </span>
                </li>
            </ul>
        </div>
        <div class="alert alert-light border mt-3 mb-0 py-2">
            <i class="bi bi-info-circle-fill text-primary me-2"></i>
            <strong>
                If an abstract fits more than one category,
                select the single category that best represents its primary subject.
            </strong>
        </div>
    </div>
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