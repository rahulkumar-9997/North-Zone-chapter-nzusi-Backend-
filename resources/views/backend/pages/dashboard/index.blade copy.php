@extends('backend.layouts.master')
@section('title','Dashboard')
@push('styles')
<style>
    .welcome-section {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 6px;
        padding: 1.5rem;
        margin-bottom: 10px;
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
                <a href="{{ route('blog-post.index') }}">
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
                </a>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6 col-12 d-flex">
            <div class="card bg-info sale-widget flex-fill">
                <a href="{{ route('manage-member.index') }}">
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
                </a>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6 col-12 d-flex">
            <div class="card bg-cyan sale-widget flex-fill">
                <a href="{{ route('manage-member.index') }}">
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
                </a>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6 col-12 d-flex">
            <div class="card bg-dark sale-widget flex-fill">
                <a href="{{ route('manage-member.index') }}">
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
                </a>
            </div>
        </div> 
        <div class="col-xl-3 col-sm-6 col-12 d-flex">
            <div class="card bg-pink sale-widget flex-fill">
                <a href="{{ route('manage-member.index') }}">
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
                </a>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6 col-12 d-flex">
            <div class="card bg-success sale-widget flex-fill">
                <a href="{{ route('abstract-submission.index') }}">
                    <div class="card-body d-flex align-items-center">
                        <span class="sale-icon bg-white text-teal">
                             <i class="ti ti-load-balancer fs-4"></i>
                        </span>
                        <div class="ms-2">
                            <p class="text-white mb-1">Total Abstract Submissions</p>
                            <div class="d-inline-flex align-items-center flex-wrap gap-2">
                                <h4 class="text-white">{{ $data['AbstractSubmission'] }}</h4>
                            </div>
                        </div>
                    </div>
                </a>
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
    {{-- GA Section Header --}}
    <div class="ga-section-divider">
        @if(request()->routeIs('dashboard'))
         <li class="nav-item pos-nav">
            <input type="text" id="daterange" class="form-control" />
         </li>
         @endif   
        <i class="ti ti-brand-google" style="color:#4285F4;font-size:15px;"></i>
        Google Analytics
        <span class="ga-period-badge">
            <i class="ti ti-calendar" style="font-size:11px;"></i>
            <span id="gaPeriodLabel">Last 30 Days</span>
        </span>
    </div>

    {{-- SECTION 1: Summary KPI — AJAX injects full HTML here --}}
    <div class="ga-card-wrap mb-3" id="wrap-summary">
        <div class="ga-loading-bar"></div>
        <div id="ga-summary">
            <div class="ga-placeholder"><span class="spinner-border"></span> Loading summary…</div>
        </div>
    </div>

    {{-- SECTION 2: Traffic Trend Chart --}}
    <div class="row mb-3">
        <div class="col-12">
            <div class="card ga-card-wrap" id="wrap-trend">
                <div class="ga-loading-bar"></div>
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        Traffic Trend
                    </h5>
                </div>
                <div class="card-body" id="ga-trend">
                    <div class="ga-placeholder"><span class="spinner-border"></span> Loading chart…</div>
                </div>
            </div>
        </div>
    </div>

    {{-- SECTION 3A: Sources / 3B: Engagement / 3C: Devices --}}
    <div class="row g-3 mb-3">
        <div class="col-xl-6 col-md-6 col-12">
            <div class="card h-100 ga-card-wrap" id="wrap-sources">
                <div class="ga-loading-bar"></div>
                <div id="ga-sources">
                    <div class="ga-placeholder"><span class="spinner-border"></span> Loading sources…</div>
                </div>
            </div>
        </div>
        <!-- <div class="col-xl-4 col-md-6 col-12">
            <div class="card h-100 ga-card-wrap" id="wrap-engagement">
                <div class="ga-loading-bar"></div>
                <div id="ga-engagement">
                    <div class="ga-placeholder"><span class="spinner-border"></span> Loading engagement…</div>
                </div>
            </div>
        </div> -->
        <div class="col-xl-6 col-md-6 col-12">
            <div class="card h-100 ga-card-wrap" id="wrap-devices">
                <div class="ga-loading-bar"></div>
                <div id="ga-devices">
                    <div class="ga-placeholder"><span class="spinner-border"></span> Loading devices…</div>
                </div>
            </div>
        </div>
        
    </div>
    <div class="row mb-3">
        <div class="col-md-6">
            <div class="card ga-card-wrap" id="wrap-toppages">
                <div class="ga-loading-bar"></div>
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="ti ti-layout-list me-2" style="color:#667eea;"></i>
                        Top Pages <small class="text-muted fw-normal ms-1" style="font-size:11px;">(GA4)</small>
                    </h5>
                </div>
                <div class="card-body" id="ga-toppages">
                    <div class="ga-placeholder"><span class="spinner-border"></span> Loading top pages…</div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card ga-card-wrap" id="wrap-countries">
                <div class="ga-loading-bar"></div>
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="ti ti-world me-2" style="color:#667eea;"></i>
                        Top Countries
                        <small class="text-muted fw-normal ms-1" style="font-size:11px;">(GA4)</small>
                    </h5>
                </div>
                <div class="card-body" id="ga-countries">
                    <div class="ga-placeholder">
                        <span class="spinner-border"></span> Loading countries…
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- <div class="row mb-3">
        <div class="col-12">
            <div class="card ga-card-wrap" id="wrap-referrers">
                <div class="ga-loading-bar"></div>
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="ti ti-link me-2" style="color:#10b981;"></i>
                        Top Referrers <small class="text-muted fw-normal ms-1" style="font-size:11px;">(GA4)</small>
                    </h5>
                </div>
                <div class="card-body" id="ga-referrers">
                    <div class="ga-placeholder"><span class="spinner-border"></span> Loading referrers…</div>
                </div>
            </div>
        </div>
    </div>  -->
</div>
@endsection
@push('scripts')
<script src="{{ asset('backend/assets/plugins/apexchart/apexcharts.min.js')}}" type="text/javascript"></script>
<script>
    window.memberAnalyticsUrl = "{{ route('member.analytics') }}";
</script>
<script src="{{ asset('backend/assets/js/pages/member-analytics.js') }}?v={{ config('app.assets_version') }}"></script>
<script>
window.GA_ROUTES = {
    summary    : "{{ route('admin.ga.summary') }}",
    trend      : "{{ route('admin.ga.trend') }}",
    sources    : "{{ route('admin.ga.sources') }}",
    engagement : "{{ route('admin.ga.engagement') }}",
    devices    : "{{ route('admin.ga.devices') }}",
    countries  : "{{ route('admin.ga.countries') }}",
    toppages   : "{{ route('admin.ga.top-pages') }}",
    referrers  : "{{ route('admin.ga.referrers') }}",
};
window.GA_CSRF = "{{ csrf_token() }}";
</script>
<script src="{{ asset('backend/assets/js/pages/ga-dashboard.js') }}?v={{ config('app.assets_version') }}"></script>
@endpush