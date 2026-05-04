@extends('backend.layouts.master')
@section('title','Dashboard')
@push('styles')
<style>
    .welcome-section {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 6px;
        padding: 1.5rem;
        margin-bottom: 2rem;
        color: white;
    }
    
    .stat-badge {
        background: rgba(255,255,255,0.2);
        backdrop-filter: blur(5px);
        border-radius: 20px;
        padding: 5px 12px;
        font-size: 0.75rem;
    }
</style>
@endpush
@section('main-content')
<div class="content">
    <div class="welcome-section d-flex align-items-center justify-content-between flex-wrap gap-3">
        <div>
            <h1 class="mb-1 fw-bold text-white">Welcome back, {{auth()->user()->name ?? 'Admin'}}!</h1>
            <p class="mb-0 opacity-75">Here's what's happening with your platform today.</p>
        </div>
        <div class="stat-badge">
            <i class="ti ti-calendar me-1"></i> {{ now()->format('l, d F Y') }}
        </div>
    </div>
    <div class="row">
        <div class="col-xl-3 col-sm-6 col-12 d-flex">
            <div class="card bg-orange sale-widget flex-fill">
                <div class="card-body d-flex align-items-center">
                    <span class="sale-icon bg-white text-primary">
                        <i class="ti ti-file-text fs-4"></i>
                    </span>
                    <div class="ms-2">
                        <p class="text-white mb-1">Blogs</p>
                        <div class="d-inline-flex align-items-center flex-wrap gap-2">
                            <h4 class="text-white">{{ $data['blog'] }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6 col-12 d-flex">
            <div class="card bg-secondary sale-widget flex-fill">
                <div class="card-body d-flex align-items-center">
                    <span class="sale-icon bg-white text-primary">
                        <i class="ti ti-tag fs-4"></i>
                    </span>
                    <div class="ms-2">
                        <p class="text-white mb-1">Total Label</p>
                        <div class="d-inline-flex align-items-center flex-wrap gap-2">
                            <h4 class="text-white">{{ $data['label'] }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6 col-12 d-flex">
            <div class="card bg-purple sale-widget flex-fill">
                <div class="card-body d-flex align-items-center">
                    <span class="sale-icon bg-white text-primary">
                        <i class="ti ti-list fs-4"></i>
                    </span>
                    <div class="ms-2">
                        <p class="text-white mb-1">Total Blogs Category</p>
                        <div class="d-inline-flex align-items-center flex-wrap gap-2">
                            <h4 class="text-white">{{ $data['BlogCategory'] }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6 col-12 d-flex">
            <div class="card bg-indigo sale-widget flex-fill">
                <div class="card-body d-flex align-items-center">
                    <span class="sale-icon bg-white text-primary">
                        <i class="ti ti-users fs-4"></i>
                    </span>
                    <div class="ms-2">
                        <p class="text-white mb-1">Total Member Type</p>
                        <div class="d-inline-flex align-items-center flex-wrap gap-2">
                            <h4 class="text-white">{{ $data['MemberType'] }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6 col-12 d-flex">
            <div class="card bg-pink sale-widget flex-fill">
                <div class="card-body d-flex align-items-center">
                    <span class="sale-icon bg-white text-secondary">
                         <i class="ti ti-user fs-4"></i>
                    </span>
                    <div class="ms-2">
                        <p class="text-white mb-1">Total Member</p>
                        <div class="d-inline-flex align-items-center flex-wrap gap-2">
                            <h4 class="text-white">{{ $data['member_total'] }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6 col-12 d-flex">
            <div class="card bg-cyan sale-widget flex-fill">
                <div class="card-body d-flex align-items-center">
                    <span class="sale-icon bg-white text-teal">
                        <i class="ti ti-clock fs-4"></i>
                    </span>
                    <div class="ms-2">
                        <p class="text-white mb-1">Total Pending Member</p>
                        <div class="d-inline-flex align-items-center flex-wrap gap-2">
                            <h4 class="text-white">{{ $data['member_pending'] }}</h4>

                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6 col-12 d-flex">
            <div class="card bg-info sale-widget flex-fill">
                <div class="card-body d-flex align-items-center">
                    <span class="sale-icon bg-white text-teal">
                         <i class="ti ti-check fs-4"></i>
                    </span>
                    <div class="ms-2">
                        <p class="text-white mb-1">Total Approved Member</p>
                        <div class="d-inline-flex align-items-center flex-wrap gap-2">
                            <h4 class="text-white">{{ $data['member_approved'] }}</h4>

                        </div>
                    </div>
                </div>
            </div>
        </div>   
        <div class="col-xl-3 col-sm-6 col-12 d-flex">
            <div class="card bg-dark sale-widget flex-fill">
                <div class="card-body d-flex align-items-center">
                    <span class="sale-icon bg-white text-teal">
                         <i class="ti ti-x fs-4"></i>
                    </span>
                    <div class="ms-2">
                        <p class="text-white mb-1">Total Rejected Member</p>
                        <div class="d-inline-flex align-items-center flex-wrap gap-2">
                            <h4 class="text-white">{{ $data['member_rejected'] }}</h4>

                        </div>
                    </div>
                </div>
            </div>
        </div>           
    </div> 
    @php
        $currentYear = date('Y');
    @endphp
    <div class="row sales-board">
        <div class="col-md-12 col-lg-12 col-sm-12 col-12 d-flex">
            <div class="card flex-fill flex-fill">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Member Analytics</h5>
                    <div class="graph-sets">
                        <div class="dropdown dropdown-wraper">
                            <button class="btn btn-white btn-sm dropdown-toggle d-flex align-items-center"
                                type="button" id="dropdown-sales" data-bs-toggle="dropdown">
                                <i data-feather="calendar" class="feather-14"></i>
                                <span id="selectedYear">{{ $currentYear }}</span>
                            </button>
                            <ul class="dropdown-menu">
                                @for ($i = 0; $i < 5; $i++)
                                    <li>
                                        <a href="javascript:void(0);" 
                                        class="dropdown-item year-option" 
                                        data-year="{{ $currentYear - $i }}">
                                            {{ $currentYear - $i }}
                                        </a>
                                    </li>
                                @endfor
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="card-body pt-1 pb-0">
                    <div id="member_analysis" class="chart-set"></div>
                </div>
            </div>
        </div>        
    </div>  
</div>
@endsection
@push('scripts')
<script src="{{ asset('backend/assets/plugins/apexchart/apexcharts.min.js')}}" type="text/javascript"></script>
<script>
    window.memberAnalyticsUrl = "{{ route('member.analytics') }}";
</script>
<script src="{{ asset('backend/assets/js/pages/member-analytics.js') }}?v={{ config('app.assets_version') }}"></script>

@endpush