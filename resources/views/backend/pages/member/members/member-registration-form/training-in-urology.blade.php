@extends('backend.layouts.master')
@section('title','Create Member :: Training in-Urology')
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

    .remove-training {
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
            <h4 class="card-title">{{ isset($member) && $member->trainings->isNotEmpty() ? 'Edit' : 'Create' }} Member (Training in-Urology)</h4>
            <div class="link-btn">
                <a href="{{ route('manage-member.index') }}" class="btn btn-info">
                    <i class="fa fa-arrow-left me-2"></i> Back to Member List
                </a>
            </div>
        </div>
        <div class="accordion-body border-top">
            <h3 class="text-info">
                Training in-Urology
            </h3>
            <div class="formse mt-3">
                <form action="{{ isset($member) && $member->trainings->isNotEmpty() ? route('manage-member.update-step4', $member->id) : route('manage-member.store-step4', $member->id) }}"
                    method="POST"
                    enctype="multipart/form-data"
                    id="member-add-fm-step4">
                    @csrf
                    @if(isset($member) && $member->trainings->isNotEmpty())
                    @method('PUT')
                    @endif

                    <div id="trainings-container">
                        @if(isset($member) && $member->trainings->isNotEmpty())
                        @foreach($member->trainings as $index => $training)
                        <div class="qualification-item">
                            @if($loop->index > 0)
                            <button type="button" class="btn btn-danger btn-sm remove-training">×</button>
                            @endif
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label class="form-label">Institution <span class="text-danger">*</span></label>
                                        <input type="text" name="trainings[{{ $index }}][institution]"
                                            class="form-control"
                                            value="{{ $training->institution }}">
                                        <div class="invalid-feedback"></div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="mb-3">
                                        <label class="form-label">From Date <span class="text-danger">*</span></label>
                                        <input type="text" name="trainings[{{ $index }}][from_date]"
                                            class="form-control datepicker"
                                            value="{{ $training->from_date }}">
                                        <div class="invalid-feedback"></div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="mb-3">
                                        <label class="form-label">To Date <span class="text-danger">*</span></label>
                                        <input type="text" name="trainings[{{ $index }}][to_date]"
                                            class="form-control datepicker"
                                            value="{{ $training->to_date }}">
                                        <div class="invalid-feedback"></div>
                                    </div>
                                </div>
                                @if($loop->index > 0)
                                <div class="col-md-2">
                                    <div style="margin-top: 30px;">
                                        <button type="button" class="btn btn-danger btn-sm remove-training">×</button>
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
                                        <label class="form-label">Institution <span class="text-danger">*</span></label>
                                        <input type="text" name="trainings[0][institution]" class="form-control">
                                        <div class="invalid-feedback"></div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label class="form-label">From Date <span class="text-danger">*</span></label>
                                        <input type="text" name="trainings[0][from_date]" class="form-control datepicker">
                                        <div class="invalid-feedback"></div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label class="form-label">To Date <span class="text-danger">*</span></label>
                                        <input type="text" name="trainings[0][to_date]" class="form-control datepicker">
                                        <div class="invalid-feedback"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>

                    <button type="button" class="btn btn-info mb-3" id="add-training">
                        <i class="fa fa-plus"></i> Add More Training in-Urology
                    </button>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">USI Member <span class="text-danger">*</span></label>
                                <div class="d-sm-flex align-items-center justify-content-between">
                                    <div class="form-check form-check-md">
                                        <input class="form-check-input" type="radio" name="usi_member" id="usi_member_yes" value="yes"
                                            {{ isset($member) && $member->usi_member == 'yes' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="usi_member_yes">Yes</label>
                                    </div>
                                    <div class="form-check form-check-md">
                                        <input class="form-check-input" type="radio" name="usi_member" id="usi_member_no" value="no"
                                            {{ isset($member) && $member->usi_member == 'no' ? 'checked' : (!isset($member) ? 'checked' : '') }}>
                                        <label class="form-check-label" for="usi_member_no">No</label>
                                    </div>
                                </div>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>

                        <div class="col-md-4" id="usi_number_field" style="{{ isset($member) && $member->usi_member == 'yes' ? 'display:block' : 'display:none' }}">
                            <div class="mb-3">
                                <label class="form-label">USI Number <span class="text-danger">*</span></label>
                                <input type="text" name="usi_number" class="form-control"
                                    value="{{ isset($member) ? $member->usi_number : '' }}">
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                    </div>

                    <input type="hidden" name="post_user" value="{{ auth()->id() }}">

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('manage-member.step3', $member->id) }}" class="btn btn-secondary">
                            <i class="fa fa-arrow-left"></i> Previous
                        </a>
                        <div>
                            <a href="{{ route('manage-member.index') }}" class="btn btn-danger">Cancel</a>
                            <button type="submit" class="btn btn-primary" id="submitbtnstep4">
                                {{ isset($member) && $member->trainings->isNotEmpty() ? 'Update and Submit' : 'Submit' }}
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
        $('.datepicker').flatpickr({
            enableTime: false,
            dateFormat: "Y-m-d"
        });
        let trainingCount = {{ isset($member) ? $member->trainings->count() : 1 }};
        $('#add-training').click(function() {
            let newHtml = `
            <div class="qualification-item">
                <button type="button" class="btn btn-danger btn-sm remove-training">×</button>
                <div class="row">  
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label">Institution <span class="text-danger">*</span></label>
                            <input type="text" name="trainings[${trainingCount}][institution]" class="form-control">
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label class="form-label">From Date <span class="text-danger">*</span></label>
                            <input type="text" name="trainings[${trainingCount}][from_date]" class="form-control datepicker">
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label class="form-label">To Date <span class="text-danger">*</span></label>
                            <input type="text" name="trainings[${trainingCount}][to_date]" class="form-control datepicker">
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>                                        
                    <div class="col-md-2">
                        <div style="margin-top: 30px;">
                            <button type="button" class="btn btn-danger btn-sm remove-training">×</button>
                        </div>
                    </div>                                        
                </div>
            </div>
        `;
            $('#trainings-container').append(newHtml);
            trainingCount++;
            $('.datepicker').flatpickr({
                enableTime: false,
                dateFormat: "Y-m-d"
            });
        });

        $(document).on('click', '.remove-training', function() {
            if ($('.qualification-item').length > 1) {
                $(this).closest('.qualification-item').remove();
            } else {
                Toastify({
                    text: "At least one training is required!",
                    duration: 3000,
                    gravity: "top",
                    position: "right",
                    className: "bg-warning"
                }).showToast();
            }
        });

        $('input[name="usi_member"]').on('change', function() {
            if ($(this).val() === 'yes') {
                $('#usi_number_field').show();
                $('#usi_number_field input').attr('required', true);
            } else {
                $('#usi_number_field').hide();
                $('#usi_number_field input').removeAttr('required');
                $('#usi_number_field input').val('');
            }
        });
    });
</script>
@endpush