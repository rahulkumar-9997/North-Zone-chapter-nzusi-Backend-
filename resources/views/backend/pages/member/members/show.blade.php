@extends('backend.layouts.master')
@section('title','Member Details - ' . ($member->name ?? ''))

@push('styles')
<style>
    .detail-card {
        border: none;
        border-radius: 10px;
        box-shadow: 0 0 20px rgba(0, 0, 0, 0.05);
        margin-bottom: 24px;
        transition: all 0.3s ease;
    }

    .detail-card:hover {
        box-shadow: 0 5px 25px rgba(0, 0, 0, 0.1);
    }

    .detail-card-header {
        background: #fff;
        border-bottom: 2px solid #f0f0f0;
        padding: 15px 20px;
        font-weight: 600;
        font-size: 16px;
        border-radius: 10px 10px 0 0;
    }

    .detail-card-header i {
        margin-right: 8px;
        color: #0d6efd;
    }

    .detail-card-body {
        padding: 20px;
    }

    .info-label {
        font-size: 12px;
        font-weight: 600;
        color: #6c757d;
        margin-bottom: 5px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .info-text {
        font-size: 15px;
        color: #2c3e50;
        margin-bottom: 15px;
        padding-bottom: 8px;
        border-bottom: 1px dashed #e9ecef;
    }

    .info-text:last-child {
        border-bottom: none;
        margin-bottom: 0;
        padding-bottom: 0;
    }

    .badge-status {
        padding: 5px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 500;
    }

    .info-box {
        background: #f8f9fa;
        padding: 12px 15px;
        border-radius: 8px;
        margin-bottom: 12px;
        border-left: 3px solid #0d6efd;
    }

    .info-box-label {
        font-size: 11px;
        color: #6c757d;
        margin-bottom: 4px;
        text-transform: uppercase;
    }

    .info-box-value {
        font-size: 14px;
        color: #2c3e50;
        font-weight: 500;
    }

    .avatar-lg {
        width: 80px;
        height: 80px;
        font-size: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
</style>
@endpush

@section('main-content')
<div class="content">
    <!-- Breadcrumb & Actions -->
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1 fw-bold">
                <i class="bi bi-person-circle me-2 text-primary"></i>
                Member Details
            </h4>            
        </div>
        <div class="mt-2 mt-sm-0">
            <a href="{{ route('manage-member.edit', $member->id) }}" class="btn btn-warning btn-sm me-2">
                <i class="bi bi-pencil-square me-1"></i> Edit
            </a>
            <a href="{{ route('manage-member.index') }}" class="btn btn-secondary btn-sm">
                <i class="bi bi-arrow-left me-1"></i> Back
            </a>
        </div>
    </div>

    <div class="row g-4">
        <!-- Left Column -->
        <div class="col-lg-4">
            <!-- Profile Card -->
            <div class="card detail-card">
                <div class="detail-card-header">
                    <i class="bi bi-person-badge"></i> Personal Information
                </div>
                <div class="detail-card-body text-center">
                    <div class="mb-3">
                        <div class="bg-primary bg-opacity-10 rounded-circle d-inline-flex avatar-lg">
                            <i class="bi bi-person-circle fs-1 text-primary"></i>
                        </div>
                        <h5 class="mt-3 mb-1">{{ $member->name }}</h5>
                        <p class="text-muted small mb-2">#{{ $member->membership_no }}</p>
                        <div>
                            @if($member->status == 'approved')
                            <span class="badge bg-success badge-status"><i class="bi bi-check-circle me-1"></i> Approved</span>
                            @elseif($member->status == 'pending')
                            <span class="badge bg-warning badge-status"><i class="bi bi-hourglass-split me-1"></i> Pending</span>
                            @else
                            <span class="badge bg-danger badge-status"><i class="bi bi-x-circle me-1"></i> Rejected</span>
                            @endif
                        </div>
                    </div>

                    <div class="text-start mt-3">
                        <div class="info-label">Member Type</div>
                        <div class="info-text">
                            <i class="bi bi-tag me-2 text-muted"></i> {{ $member->type->title ?? 'N/A' }}
                        </div>

                        <div class="info-label">Gender</div>
                        <div class="info-text">
                            <i class="bi bi-gender-{{ $member->gender == 'male' ? 'male' : ($member->gender == 'female' ? 'female' : 'ambiguous') }} me-2 text-muted"></i>
                            {{ ucfirst($member->gender ?? 'Not specified') }}
                        </div>

                        <div class="info-label">Date of Birth</div>
                        <div class="info-text">
                            <i class="bi bi-calendar me-2 text-muted"></i>
                            {{ $member->dob ? $member->dob->format('d M Y') : 'Not specified' }}
                        </div>

                        <div class="info-label">City</div>
                        <div class="info-text">
                            <i class="bi bi-building me-2 text-muted"></i>
                            {{ $member->city_name ?? 'Not specified' }}
                        </div>

                        <div class="info-label">Created At</div>
                        <div class="info-text">
                            <i class="bi bi-clock me-2 text-muted"></i>
                            {{ $member->created_at ? $member->created_at->format('d M Y, h:i A') : 'N/A' }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Contact Card -->
            <div class="card detail-card">
                <div class="detail-card-header">
                    <i class="bi bi-envelope-paper"></i> Contact Information
                </div>
                <div class="detail-card-body">
                    <div class="info-label">Email Address</div>
                    <div class="info-text">
                        <i class="bi bi-envelope me-2 text-muted"></i>
                        <a href="mailto:{{ $member->email }}" class="text-decoration-none">{{ $member->email }}</a>
                    </div>

                    <div class="info-label">Mobile Number</div>
                    <div class="info-text">
                        <i class="bi bi-phone me-2 text-muted"></i>
                        {{ $member->mobile_no ?? 'Not provided' }}
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column -->
        <div class="col-lg-8">
            <!-- Address Card -->
            <div class="card detail-card">
                <div class="detail-card-header">
                    <i class="bi bi-geo-alt"></i> Address Information
                    <span class="badge bg-secondary ms-2">Preferred: {{ ucfirst($member->preferred_address ?? 'Not set') }}</span>
                </div>
                <div class="detail-card-body">
                    @if($member->preferred_address == 'office' && $member->officeAddress)
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="info-label">Office Address</div>
                            <p class="mb-2">{{ $member->officeAddress->office_address ?? 'N/A' }}</p>

                            <div class="info-label mt-2">Office Location</div>
                            <p class="mb-2">{{ $member->officeAddress->office_city ?? '' }}, {{ $member->officeAddress->office_state ?? '' }} - {{ $member->officeAddress->office_pin ?? '' }}</p>
                        </div>
                        <div class="col-md-6">
                            <div class="info-label">Office Phone</div>
                            <p class="mb-2">{{ $member->officeAddress->office_phone ?? 'N/A' }}</p>

                            <div class="info-label">Office Email</div>
                            <p class="mb-2">{{ $member->officeAddress->office_email ?? 'N/A' }}</p>

                            <div class="info-label">Office Website</div>
                            <p class="mb-2">{{ $member->officeAddress->office_website ?? 'N/A' }}</p>
                        </div>
                    </div>
                    @elseif($member->residenceAddress)
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="info-label">Residence Address</div>
                            <p class="mb-2">{{ $member->residenceAddress->residence_address ?? 'N/A' }}</p>

                            <div class="info-label mt-2">Residence Location</div>
                            <p class="mb-2">{{ $member->residenceAddress->residence_city ?? '' }}, {{ $member->residenceAddress->residence_state ?? '' }} - {{ $member->residenceAddress->residence_pin ?? '' }}</p>
                        </div>
                        <div class="col-md-6">
                            <div class="info-label">Residence Phone</div>
                            <p class="mb-2">{{ $member->residenceAddress->residence_phone ?? 'N/A' }}</p>

                            <div class="info-label">Residence Email</div>
                            <p class="mb-2">{{ $member->residenceAddress->residence_email ?? 'N/A' }}</p>

                            <div class="info-label">Residence Website</div>
                            <p class="mb-2">{{ $member->residenceAddress->residence_website ?? 'N/A' }}</p>
                        </div>
                    </div>
                    @else
                    <div class="text-center py-4 text-muted">
                        <i class="bi bi-info-circle me-1"></i> No address information available
                    </div>
                    @endif
                </div>
            </div>

            <!-- Designation Card -->
            <div class="card detail-card">
                <div class="detail-card-header">
                    <i class="bi bi-briefcase"></i> Present Appointment & Designation
                </div>
                <div class="detail-card-body">
                    @if($member->presentDesignations->isNotEmpty())
                    @foreach($member->presentDesignations as $designation)
                    <div class="row g-3 mb-3 pb-2 border-bottom">
                        <div class="col-md-5">
                            <div class="info-label">Designation</div>
                            <p class="fw-medium mb-0">{{ $designation->designation }}</p>
                        </div>
                        <div class="col-md-5">
                            <div class="info-label">Institution</div>
                            <p class="fw-medium mb-0">{{ $designation->institution }}</p>
                        </div>
                        <div class="col-md-2">
                            <div class="info-label">Year</div>
                            <p class="fw-medium mb-0">{{ $designation->year_of_joining }}</p>
                        </div>
                    </div>
                    @endforeach
                    @else
                    <div class="text-center py-4 text-muted">
                        <i class="bi bi-info-circle me-1"></i> No designation information available
                    </div>
                    @endif
                </div>
            </div>

            <!-- Academic Card -->
            <div class="card detail-card">
                <div class="detail-card-header">
                    <i class="bi bi-mortarboard"></i> Academic Qualifications
                </div>
                <div class="detail-card-body">
                    @if($member->academicQualifications->isNotEmpty())
                    @foreach($member->academicQualifications as $qualification)
                    <div class="info-box">
                        <div class="row g-2">
                            <div class="col-md-5">
                                <div class="info-box-label">Degree/Diploma</div>
                                <div class="info-box-value">{{ $qualification->degree }}</div>
                            </div>
                            <div class="col-md-5">
                                <div class="info-box-label">Institution/University</div>
                                <div class="info-box-value">{{ $qualification->institution }}</div>
                            </div>
                            <div class="col-md-2">
                                <div class="info-box-label">Year</div>
                                <div class="info-box-value">{{ $qualification->year_of_passing }}</div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                    @else
                    <div class="text-center py-4 text-muted">
                        <i class="bi bi-info-circle me-1"></i> No academic qualifications available
                    </div>
                    @endif
                </div>
            </div>

            <!-- Training Card -->
            <div class="card detail-card">
                <div class="detail-card-header">
                    <i class="bi bi-hospital"></i> Urology Training
                </div>
                <div class="detail-card-body">
                    <div class="mb-3 pb-2 border-bottom">
                        <span class="info-label">USI Member:</span>
                        @if($member->usi_member == 'yes')
                        <span class="badge bg-success ms-2"><i class="bi bi-check-circle me-1"></i> Yes</span>
                        @if($member->usi_number)
                        <span class="badge bg-info ms-2">USI No: {{ $member->usi_number }}</span>
                        @endif
                        @else
                        <span class="badge bg-secondary ms-2"><i class="bi bi-x-circle me-1"></i> No</span>
                        @endif
                    </div>

                    @if($member->trainings->isNotEmpty())
                    <div class="info-label mb-2">Training Details:</div>
                    @foreach($member->trainings as $training)
                    <div class="info-box">
                        <div class="row g-2">
                            <div class="col-md-6">
                                <div class="info-box-label">Institution</div>
                                <div class="info-box-value">{{ $training->institution }}</div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-box-label">Duration</div>
                                <div class="info-box-value">
                                    {{ \Carbon\Carbon::parse($training->from_date)->format('d M Y') }} -
                                    {{ \Carbon\Carbon::parse($training->to_date)->format('d M Y') }}
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                    @else
                    <div class="text-center py-4 text-muted">
                        <i class="bi bi-info-circle me-1"></i> No training information available
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection