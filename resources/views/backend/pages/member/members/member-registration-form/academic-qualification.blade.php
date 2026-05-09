@extends('backend.layouts.master')
@section('title','Create Member Academic qualification Form')
@push('styles')
<style>
    .qualification-item {
        position: relative;
        margin-bottom: 20px;
        padding: 15px;
        border: 1px solid #dee2e6;
        border-radius: 5px;
        background: #f8f9fa;
    }

    .remove-qualification {
        position: absolute;
        top: 10px;
        right: 10px;
    }

    .is-invalid {
        border-color: #dc3545 !important;
    }

    .invalid-feedback {
        display: block;
        width: 100%;
        margin-top: 0.25rem;
        font-size: 0.875em;
        color: #dc3545;
    }
</style>
@endpush

@section('main-content')
<div class="content">
    <div class="card">
        <div class="card-header d-flex align-items-center justify-content-between flex-wrap row-gap-3">
            <h4 class="card-title">{{ isset($member) && $member->academicQualifications->isNotEmpty() ? 'Edit' : 'Create' }} Member (Academic Qualification Form)</h4>
            <div class="link-btn">
                <a href="{{ route('manage-member.index') }}" class="btn btn-info">
                    <i class="fa fa-arrow-left me-2"></i> Back to Member List
                </a>
            </div>
        </div>
        <div class="accordion-body border-top">
            <h3 class="text-info">
                Academic qualification
            </h3>
            <div class="formse mt-3">
                <form action="{{ isset($member) && $member->academicQualifications->isNotEmpty() ? route('manage-member.update-step3', $member->id) : route('manage-member.store-step3', $member->id) }}"
                    method="POST"
                    enctype="multipart/form-data"
                    id="member-add-fm-step3">
                    @csrf
                    @if(isset($member) && $member->academicQualifications->isNotEmpty())
                    @method('PUT')
                    @endif

                    <div id="qualifications-container">
                        @if(isset($member) && $member->academicQualifications->isNotEmpty())
                        @foreach($member->academicQualifications as $index => $qualification)
                        <div class="qualification-item">
                            @if($loop->index > 0)
                            <button type="button" class="btn btn-danger btn-sm remove-qualification">×</button>
                            @endif
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label class="form-label">Degree/Diploma <span class="text-danger">*</span></label>
                                        <input type="text" name="qualifications[{{ $index }}][degree]"
                                            class="form-control"
                                            value="{{ $qualification->degree }}">
                                        <div class="invalid-feedback"></div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label class="form-label">Institution/University <span class="text-danger">*</span></label>
                                        <input type="text" name="qualifications[{{ $index }}][institution]"
                                            class="form-control"
                                            value="{{ $qualification->institution }}">
                                        <div class="invalid-feedback"></div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="mb-3">
                                        <label class="form-label">Year of Passing <span class="text-danger">*</span></label>
                                        <input type="number" name="qualifications[{{ $index }}][year_of_passing]"
                                            class="form-control"
                                            value="{{ $qualification->year_of_passing }}"
                                            min="1900" max="{{ date('Y') }}">
                                        <div class="invalid-feedback"></div>
                                    </div>
                                </div>
                                @if($loop->index > 0)
                                <div class="col-md-1">
                                    <div class="mt-4">
                                        <button type="button" class="btn btn-danger btn-sm remove-qualification">×</button>
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>
                        @endforeach
                        @else
                        <div class="qualification-item">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label class="form-label">Degree/Diploma <span class="text-danger">*</span></label>
                                        <input type="text" name="qualifications[0][degree]" class="form-control">
                                        <div class="invalid-feedback"></div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label class="form-label">Institution/University <span class="text-danger">*</span></label>
                                        <input type="text" name="qualifications[0][institution]" class="form-control">
                                        <div class="invalid-feedback"></div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label class="form-label">Year of Passing <span class="text-danger">*</span></label>
                                        <input type="number" name="qualifications[0][year_of_passing]"
                                            class="form-control" min="1900" max="{{ date('Y') }}">
                                        <div class="invalid-feedback"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>

                    <button type="button" class="btn btn-info mb-3" id="add-qualification">
                        <i class="fa fa-plus"></i> Add More Qualification
                    </button>

                    <input type="hidden" name="post_user" value="{{ auth()->id() }}">

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('manage-member.step2', $member->id) }}" class="btn btn-secondary">
                            <i class="fa fa-arrow-left"></i> Previous
                        </a>
                        <div>
                            @if(isset($member))
                                <a href="{{ route('manage-member.step4', $member->id) }}" class="btn btn-orange me-2">Next Form</a>
                            @endif
                            <a href="{{ route('manage-member.index') }}" class="btn btn-secondary me-2">Cancel</a>
                            <button type="submit" class="btn btn-primary" id="submitbtnstep3">
                                {{ isset($member) && $member->academicQualifications->isNotEmpty() ? 'Update and Next' : 'Save and Next' }}
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection
@push('scripts')
<script src="{{ asset('backend/assets/js/pages/member-registration.js') }}"></script>
<script>
    $(document).ready(function() {
        let qualificationCount = {{ isset($member) ? $member->academicQualifications->count() : 1 }};
        $('#add-qualification').click(function() {
            let newHtml = `
            <div class="qualification-item">                
                <div class="row">  
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label">Degree/Diploma <span class="text-danger">*</span></label>
                            <input type="text" name="qualifications[${qualificationCount}][degree]" class="form-control">
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label">Institution/University <span class="text-danger">*</span></label>
                            <input type="text" name="qualifications[${qualificationCount}][institution]" class="form-control">
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label class="form-label">Year of Passing <span class="text-danger">*</span></label>
                            <input type="number" name="qualifications[${qualificationCount}][year_of_passing]" class="form-control" min="1900" max="{{ date('Y') }}">
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>         
                    <div class="col-md-1">
                        <div class="mt-4">
                            <button type="button" class="btn btn-danger btn-sm remove-qualification">×</button>
                        </div>
                    </div>         
                </div>
            </div>
        `;
            $('#qualifications-container').append(newHtml);
            qualificationCount++;
        });

        $(document).on('click', '.remove-qualification', function() {
            if ($('.qualification-item').length > 1) {
                $(this).closest('.qualification-item').remove();
            } else {
                Toastify({
                    text: "At least one qualification is required!",
                    duration: 3000,
                    gravity: "top",
                    position: "right",
                    className: "bg-warning"
                }).showToast();
            }
        });
    });
</script>
@endpush