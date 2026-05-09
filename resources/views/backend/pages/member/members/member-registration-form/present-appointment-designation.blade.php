@extends('backend.layouts.master')
@section('title','Create Member Present appointment & designation')
@push('styles')
@endpush
@section('main-content')
<div class="content">
    <div class="card">
        <div class="card-header d-flex align-items-center justify-content-between flex-wrap row-gap-3">
            <h4 class="card-title">
                {{ isset($member) && $member->presentDesignations->isNotEmpty() ? 'Edit' : 'Create' }} Member (Present appointment & designation)
            </h4>
            <div class="link-btn">                
                <a href="{{ route('manage-member.create') }}"
                    class="btn btn-info">
                    <i class="fa fa-arrow-left me-2"></i> Back to Member List
                </a>
            </div>
        </div>
        <div class="accordion-body border-top">
            <h3 class="text-info">
                Present appointment & designation 
            </h3>
            <div class="formse mt-3">
                <form action="{{ isset($member) && $member->presentDesignations->isNotEmpty() ? route('manage-member.update-step2', $member->id) : route('manage-member.store-step2', $member->id) }}" method="POST" enctype="multipart/form-data" id="member-add-fm-step2">
                    @csrf
                    @if(isset($member) && $member->presentDesignations->isNotEmpty())
                        @method('PUT')
                    @endif
                    <div class="row">  
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Designation <span class="text-danger">*</span></label>
                                <input type="text" name="designation" class="form-control" id="designation"
                                value="{{ isset($member->presentDesignations[0]) ? $member->presentDesignations[0]->designation : '' }}" 
                                >
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Institution <span class="text-danger">*</span></label>
                                <input type="text" name="institution" class="form-control" id="institution" value="{{ isset($member->presentDesignations[0]) ? $member->presentDesignations[0]->institution : '' }}">
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Year of Joining <span class="text-danger">*</span></label>
                                <input type="text" name="year_of_joining" class="form-control" id="year_of_joining" value="{{ isset($member->presentDesignations[0]) ? $member->presentDesignations[0]->year_of_joining : '' }}" min="1900" max="{{ date('Y') }}">
                            </div>
                        </div>         
                    </div>
                    <input type="hidden" name="post_user" value="{{ auth()->id() }}">
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('manage-member.step1', $member->id) }}" class="btn btn-secondary">
                            <i class="fa fa-arrow-left"></i> Previous
                        </a>
                        <div>
                            @if(isset($member))
                                <a href="{{ route('manage-member.step3', $member->id) }}" class="btn btn-orange me-2">Next Form</a>
                            @endif
                            <a href="{{ route('manage-member.index') }}" class="btn btn-secondary me-2">Cancel</a>
                            <button type="submit" class="btn btn-primary" id="submitbtnstep2">
                                {{ isset($member) && $member->presentDesignations->isNotEmpty() ? 'Update and Next' : 'Save and Next' }}
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
$(document).ready(function(){
    $('.datepicker').flatpickr({
        enableTime: false,
        dateFormat: "Y-m-d"
    });
});
</script>

@endpush