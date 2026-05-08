@extends('backend.layouts.master')
@section('title','Create Member')
@push('styles')
@endpush
@section('main-content')
<div class="content">
    <div class="card">
        <div class="card-header d-flex align-items-center justify-content-between flex-wrap row-gap-3">
            <h4 class="card-title">Create Member</h4>
            <div class="link-btn">
                
                <a href="{{ route('manage-member.create') }}"
                    class="btn btn-info">
                    <i class="fa fa-arrow-left me-2"></i> Back to Member List
                </a>
            </div>
        </div>
        <div class="accordion-body border-top">
            <form action="{{ route('manage-member.store-step1') }}" method="POST" enctype="multipart/form-data" id="member-add-fm-step1">
                @csrf
                <div class="row">
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label">
                                Select Member Type                                 
                                <span class="text-danger">*</span>
                                <a href="javascript:void(0);" class="btn btn-primary btn-md d-inline-flex align-items-center btn-sm"
                                href="javascript:void(0);" 
                                data-route="{{ route('blog-category.create') }}"
                                data-size="lg"
                                data-title="Create Blog Category"
                                data-blog-category-add="true"
                                data-type="select">
                                    Add New Member Type
                                </a>
                            </label>
                            <select class="form-control" name="member_type" id="member_type">
                                <option value="">Select Member Type</option>
                                @foreach($memberTypes as $MemberType)
                                    <option 
                                    value="{{ $MemberType->id }}"
                                    {{ old('member_type') == $MemberType->id ? 'selected' : '' }}>{{ $MemberType->title }}</option>
                                @endforeach
                            </select>                            
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label">Membership Number <span class="text-danger">*</span></label>
                            <input type="text" name="membership_no" class="form-control" id="membership_no">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label">Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" id="name">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label">Email <span class="text-danger">*</span></label>
                            <input type="email" name="email" class="form-control" id="email">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label">Mobile Number</label>
                            <input type="text" name="mobile_no" class="form-control" id="mobile_no">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label">Gender</label>
                            <select name="gender" class="form-control" id="gender">
                                <option value="">Select Gender</option>
                                <option value="male">Male</option>
                                <option value="female">Female</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label">City Name</label>
                            <input type="text" name="city_name" class="form-control" id="city_name">
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label">Date of Birth</label>
                            <input type="text" name="dob" class="form-control datepicker" id="dob">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label">Preferred Address</label>
                            <div class="d-sm-flex align-items-center justify-content-between">
                                <div class="form-check form-check-md">
                                    <input class="form-check-input" type="radio" name="preferred_address" id="preferred_office" value="office">
                                    <label class="form-check-label" for="preferred_office">Office</label>
                                </div>
                                <div class="form-check form-check-md">
                                    <input class="form-check-input" type="radio" name="preferred_address" id="preferred_residence" value="residence" checked>
                                    <label class="form-check-label" for="preferred_residence">Residence</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-12" id="office_address">
                        <h5>Office Address</h5>
                        <hr>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Office State</label>
                                    <input type="text" name="office_state" class="form-control" value="{{ $member->officeAddress->office_state ?? '' }}" id="office_state">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Office City</label>
                                    <input type="text" name="office_city" class="form-control" value="{{ $member->officeAddress->office_city ?? '' }}" id="office_city">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Office PIN</label>
                                    <input type="text" name="office_pin" class="form-control" value="{{ $member->officeAddress->office_pin ?? '' }}" id="office_pin">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Office Address</label>
                                    <textarea name="office_address" class="form-control" id="office_address" rows="2">{{ $member->officeAddress->office_address ?? '' }}</textarea>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Office Phone</label>
                                    <input type="text" name="office_phone" class="form-control" value="{{ $member->officeAddress->office_phone ?? '' }}" id="office_phone">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Office Email</label>
                                    <input type="email" name="office_email" class="form-control" value="{{ $member->officeAddress->office_email ?? '' }}" id="office_email">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Office Website</label>
                                    <input type="url" name="office_website" class="form-control" value="{{ $member->officeAddress->office_website ?? '' }}" id="office_website">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-12" id="residence_address">
                        <h5>Residence Address</h5>
                        <hr>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Residence State</label>
                                    <input type="text"name="residence_state" class="form-control" value="{{ old('residence_state') }}" id="residence_state">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Residence City</label>
                                    <input type="text" name="residence_city" class="form-control" value="{{ old('residence_city') }}" id="residence_city">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Residence PIN</label>
                                    <input type="text" name="office_pin" class="form-control" value="{{ old('residence_pin') }}" id="office_pin">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Residence Address</label>
                                    <textarea name="residence_address" id="residence_address" class="form-control" rows="2">{{ old('residence_address') }}</textarea>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Residence Phone</label>
                                    <input type="text" name="residence_phone" id="residence_phone" class="form-control" value="{{ old('residence_phone') }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Residence Email</label>
                                    <input type="email" name="residence_email" id="residence_email" class="form-control" value="{{ old('residence_email') }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Residence Website</label>
                                    <input type="url" name="residence_website" id="residence_website" class="form-control" value="{{ old('residence_website') }}">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label">Status</label>
                            <div class="d-sm-flex align-items-center justify-content-between">
                                <div class="form-check form-check-md">
                                    <input class="form-check-input" type="radio" name="status" id="status_pending" value="pending">
                                    <label class="form-check-label" for="Radio-md">
                                        Pending
                                    </label>
                                </div>
                                <div class="form-check form-check-md">
                                    <input class="form-check-input" type="radio" name="status" id="status_approved" value="approved" checked>
                                    <label class="form-check-label" for="Radio-md">
                                        Approved
                                    </label>
                                </div>
                                <div class="form-check form-check-md">
                                    <input class="form-check-input" type="radio" name="status" id="status_rejected" value="rejected">
                                    <label class="form-check-label" for="Radio-md">
                                        Rejected
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>             
                </div>
                <input type="hidden" name="post_user" value="{{ auth()->id() }}">
                <div class="d-flex justify-content-end">
                    <a href="{{ route('blog-post.index') }}" class="btn btn-secondary me-2">Cancel</a>
                    <button type="submit" class="btn btn-primary" id="submitbtnstep1">Save and Next</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection
@push('scripts')
<script src="{{ asset('backend/assets/js/pages/member-registration.js') }}"></script>
<script>
$(document).ready(function(){
    $('.datepicker').flatpickr({
        enableTime: false,
        dateFormat: "Y-m-d"
    });
});
</script>
<script>
$(document).ready(function () {
    function toggleAddress() {
        let selected = $('input[name="preferred_address"]:checked').val();
        if (selected === 'office') {
            $('#office_address').show();
            $('#residence_address').hide();
        } else {
            $('#office_address').hide();
            $('#residence_address').show();
        }
    }
    toggleAddress();
    $('input[name="preferred_address"]').on('change', function () {
        toggleAddress();
    });
});
</script>
@endpush