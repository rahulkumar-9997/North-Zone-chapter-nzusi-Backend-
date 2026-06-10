@extends('backend.layouts.master')
@section('title','Dashboard')

@push('styles')
<style>
    .welcome-section {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 10px;
        padding: 1.4rem 1.6rem;
        margin-bottom: 14px;
        color: white;
    }

    .stat-badge {
        background: rgba(255, 255, 255, 0.18);
        backdrop-filter: blur(6px);
        border-radius: 20px;
        padding: 5px 14px;
        font-size: 0.75rem;
        white-space: nowrap;
    }
    .ga-datebar {
        position: sticky;
        top: 0;
        z-index: 999;
        background: #ffffff;
        border-bottom: 1px solid #e5e7eb;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.07);
        padding: 10px 16px;
        margin: 0 3px 18px;
        /* stretch full width */
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 10px;
    }

    .ga-datebar-label {
        font-size: 11px;
        font-weight: 700;
        color: #6b7280;
        text-transform: uppercase;
        letter-spacing: .6px;
        white-space: nowrap;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .ga-datebar-label i {
        font-size: 14px;
        color: #667eea;
    }
    #daterange {
        border: 1.5px solid #d1d5db;
        border-radius: 8px;
        padding: 6px 12px;
        font-size: 12.5px;
        font-weight: 600;
        color: #374151;
        background: #f9fafb;
        cursor: pointer;
        min-width: 220px;
        transition: border-color .2s, box-shadow .2s;
        outline: none;
    }

    #daterange:focus,
    #daterange:hover {
        border-color: #667eea;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, .15);
    }

    /* Active period badge */
    .ga-active-period {
        margin-left: auto;
        display: flex;
        align-items: center;
        gap: 6px;
        background: #ede9fe;
        color: #5b21b6;
        border-radius: 8px;
        padding: 5px 12px;
        font-size: 11.5px;
        font-weight: 600;
        white-space: nowrap;
    }

    .ga-active-period i {
        font-size: 12px;
    }

    /* Refresh spinner inside bar */
    .ga-bar-spinner {
        display: none;
        align-items: center;
        gap: 5px;
        font-size: 11px;
        color: #667eea;
        font-weight: 600;
    }

    .ga-bar-spinner.show {
        display: flex;
    }

    .ga-spin {
        animation: ga-rotate .7s linear infinite;
        display: inline-block;
    }

    @keyframes ga-rotate {
        to {
            transform: rotate(360deg);
        }
    }
    .ga-section-divider {
        display: flex;
        align-items: center;
        gap: 10px;
        margin: 20px 0 14px;
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 1px;
        color: #6b7280;
    }

    .ga-section-divider::after {
        content: '';
        flex: 1;
        height: 1px;
        background: #e5e7eb;
    }

    .ga-period-badge {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        background: #ede9fe;
        color: #5b21b6;
        border-radius: 6px;
        padding: 3px 10px;
        font-size: 11px;
        font-weight: 600;
    }

    .ga-card-wrap {
        position: relative;
    }

    /* Animated shimmer bar on card top */
    .ga-loading-bar {
        display: none;
        height: 3px;
        background: linear-gradient(90deg,
                transparent 0%,
                #667eea 30%,
                #764ba2 60%,
                transparent 100%);
        background-size: 300% 100%;
        animation: ga-shimmer 1.4s ease-in-out infinite;
        border-radius: 6px 6px 0 0;
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        z-index: 5;
    }

    @keyframes ga-shimmer {
        0% {
            background-position: 100% 0;
        }

        100% {
            background-position: -100% 0;
        }
    }

    .ga-card-wrap.ga-loading .ga-loading-bar {
        display: block;
    }

    /* Fade + blur content while loading */
    .ga-card-wrap.ga-loading>*:not(.ga-loading-bar) {
        opacity: 0.55;
        filter: blur(0.5px);
        pointer-events: none;
        transition: opacity .25s, filter .25s;
    }

    /* ══════════════════════════════════════════════════
   SMOOTH CONTENT FADE-IN (when HTML injects)
══════════════════════════════════════════════════ */
    .ga-fade-in {
        animation: ga-fade .35s ease-out forwards;
    }

    @keyframes ga-fade {
        from {
            opacity: 0;
            transform: translateY(6px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    .ga-placeholder {
        padding: 32px;
        text-align: center;
        color: #9ca3af;
        font-size: 13px;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
    }

    .ga-placeholder .spinner-border {
        width: 18px;
        height: 18px;
        border-width: 2px;
        color: #667eea;
        flex-shrink: 0;
    }

    /* Skeleton line animation */
    .skel-line {
        background: linear-gradient(90deg, #f3f4f6 25%, #e9ecef 50%, #f3f4f6 75%);
        background-size: 400% 100%;
        animation: skel-move 1.4s ease infinite;
        border-radius: 5px;
        display: block;
        height: 13px;
        margin-bottom: 8px;
    }

    @keyframes skel-move {
        to {
            background-position: -400% 0;
        }
    }
    .ga-error-block {
        padding: 12px 16px;
        background: #fef2f2;
        border: 1px solid #fecaca;
        border-radius: 8px;
        color: #dc2626;
        font-size: 12.5px;
        display: flex;
        align-items: center;
        gap: 8px;
        animation: ga-fade .3s ease-out;
    }
    .daterangepicker {
        z-index: 1055 !important;
        border-radius: 10px !important;
        box-shadow: 0 8px 30px rgba(0, 0, 0, .14) !important;
        border: 1px solid #e5e7eb !important;
        font-family: inherit !important;
        font-size: 13px !important;
    }

    .daterangepicker .drp-selected {
        font-size: 11px !important;
    }

    .daterangepicker td.active,
    .daterangepicker td.active:hover {
        background: #667eea !important;
        border-radius: 6px !important;
    }

    .daterangepicker td.in-range {
        background: #ede9fe !important;
        color: #5b21b6 !important;
    }

    .daterangepicker td.start-date {
        border-radius: 6px 0 0 6px !important;
    }

    .daterangepicker td.end-date {
        border-radius: 0 6px 6px 0 !important;
    }

    .daterangepicker .ranges li.active {
        background: #667eea !important;
        border-radius: 6px !important;
    }

    .daterangepicker .ranges li:hover {
        background: #ede9fe !important;
        color: #5b21b6 !important;
        border-radius: 6px !important;
    }

    .daterangepicker .applyBtn {
        background: #667eea !important;
        border-color: #667eea !important;
        border-radius: 6px !important;
    }

    .daterangepicker .cancelBtn {
        border-radius: 6px !important;
    }
    .dash-animate {
        animation: slide-up .4s ease-out both;
    }

    @keyframes slide-up {
        from {
            opacity: 0;
            transform: translateY(14px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .dash-animate:nth-child(1) {
        animation-delay: .04s;
    }

    .dash-animate:nth-child(2) {
        animation-delay: .08s;
    }

    .dash-animate:nth-child(3) {
        animation-delay: .12s;
    }

    .dash-animate:nth-child(4) {
        animation-delay: .16s;
    }

    .dash-animate:nth-child(5) {
        animation-delay: .20s;
    }

    .dash-animate:nth-child(6) {
        animation-delay: .24s;
    }
</style>
@endpush

@section('main-content')
@php $currentYear = date('Y'); @endphp
<div class="content">
    {{-- Welcome --}}
    <div class="welcome-section d-flex align-items-center justify-content-between flex-wrap gap-3 dash-animate">
        <div>
            <h1 class="mb-1 fw-bold text-white">Welcome back, {{ auth()->user()->name ?? 'Admin' }}!</h1>
            <p class="mb-0 opacity-75">Here's what's happening with your platform today.</p>
        </div>
        <div class="stat-badge">
            <i class="ti ti-calendar me-1"></i> {{ now()->format('l, d F Y') }}
        </div>
    </div>
    <div class="row">
        <div class="col-xl-3 col-sm-6 col-12 d-flex dash-animate">
            <div class="card bg-orange sale-widget flex-fill">
                <a href="{{ route('blog-post.index') }}">
                    <div class="card-body d-flex align-items-center">
                        <span class="sale-icon bg-white text-primary"><i class="ti ti-file-text fs-4"></i></span>
                        <div class="ms-2">
                            <p class="text-white mb-1">Blogs</p>
                            <h4 class="text-white">{{ $data['blog'] }}</h4>
                        </div>
                    </div>
                </a>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6 col-12 d-flex dash-animate">
            <div class="card bg-info sale-widget flex-fill">
                <a href="{{ route('manage-member.index') }}">
                    <div class="card-body d-flex align-items-center">
                        <span class="sale-icon bg-white text-teal"><i class="ti ti-check fs-4"></i></span>
                        <div class="ms-2">
                            <p class="text-white mb-1">Total Approved Member</p>
                            <h4 class="text-white">{{ $data['member_approved'] }}</h4>
                        </div>
                    </div>
                </a>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6 col-12 d-flex dash-animate">
            <div class="card bg-cyan sale-widget flex-fill">
                <a href="{{ route('manage-member.index') }}">
                    <div class="card-body d-flex align-items-center">
                        <span class="sale-icon bg-white text-teal"><i class="ti ti-clock fs-4"></i></span>
                        <div class="ms-2">
                            <p class="text-white mb-1">Total Pending Member</p>
                            <h4 class="text-white">{{ $data['member_pending'] }}</h4>
                        </div>
                    </div>
                </a>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6 col-12 d-flex dash-animate">
            <div class="card bg-dark sale-widget flex-fill">
                <a href="{{ route('manage-member.index') }}">
                    <div class="card-body d-flex align-items-center">
                        <span class="sale-icon bg-white text-teal"><i class="ti ti-x fs-4"></i></span>
                        <div class="ms-2">
                            <p class="text-white mb-1">Total Rejected Member</p>
                            <h4 class="text-white">{{ $data['member_rejected'] }}</h4>
                        </div>
                    </div>
                </a>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6 col-12 d-flex dash-animate">
            <div class="card bg-pink sale-widget flex-fill">
                <a href="{{ route('manage-member.index') }}">
                    <div class="card-body d-flex align-items-center">
                        <span class="sale-icon bg-white text-secondary"><i class="ti ti-user fs-4"></i></span>
                        <div class="ms-2">
                            <p class="text-white mb-1">Total Member</p>
                            <h4 class="text-white">{{ $data['member_total'] }}</h4>
                        </div>
                    </div>
                </a>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6 col-12 d-flex dash-animate">
            <div class="card bg-success sale-widget flex-fill">
                <a href="{{ route('abstract-submission.index') }}">
                    <div class="card-body d-flex align-items-center">
                        <span class="sale-icon bg-white text-teal"><i class="ti ti-load-balancer fs-4"></i></span>
                        <div class="ms-2">
                            <p class="text-white mb-1">Total Abstract Submissions</p>
                            <h4 class="text-white">{{ $data['AbstractSubmission'] }}</h4>
                        </div>
                    </div>
                </a>
            </div>
        </div>
    </div>
    <div class="row sales-board">
        <div class="col-md-12 col-lg-12 col-sm-12 col-12 d-flex">
            <div class="card flex-fill">
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
    <div class="ga-datebar dash-animate">
        <span class="ga-datebar-label">
            <i class="ti ti-brand-google"></i>
            <h3>Analytics Period</h3>
        </span>
        <input type="text"
            id="daterange"
            class="form-control"
            placeholder="Select date range…"
            readonly />
        <div class="ga-bar-spinner" id="gaBarSpinner">
            <span class="ga-spin"><i class="ti ti-refresh" style="font-size:13px;"></i></span>
            Refreshing data…
        </div>
        <div class="ga-active-period">
            <i class="ti ti-calendar-check"></i>
            <span id="gaPeriodLabel">Last 30 Days</span>
        </div>
    </div>
    <div class="ga-section-divider">
        <i class="ti ti-brand-google" style="color:#4285F4;font-size:15px;"></i>
        Google Analytics
        <span class="ga-period-badge">
            <i class="ti ti-calendar" style="font-size:11px;"></i>
            <span id="gaPeriodBadge">Last 30 Days</span>
        </span>
    </div>
    <div class="ga-card-wrap mb-3" id="wrap-summary">
        <div class="ga-loading-bar"></div>
        <div id="ga-summary">
            {{-- Skeleton --}}
            <div class="row g-3">
                @for($i = 0; $i < 4; $i++)
                    <div class="col-xl-3 col-sm-6 col-12">
                    <div class="card p-3">
                        <span class="skel-line" style="width:40%;height:10px;"></span>
                        <span class="skel-line" style="width:60%;height:26px;margin-top:8px;"></span>
                        <span class="skel-line" style="width:35%;height:10px;"></span>
                    </div>
            </div>
            @endfor
        </div>
    </div>
</div>

{{-- ══════════════════════════════════════════════
         SECTION 2 — TRAFFIC TREND CHART
    ══════════════════════════════════════════════ --}}
<div class="row mb-3">
    <div class="col-12">
        <div class="card ga-card-wrap" id="wrap-trend">
            <div class="ga-loading-bar"></div>
            <div class="card-header d-flex align-items-center justify-content-between">
                <h4 class="card-title mb-0">
                    Traffic Trend
                </h4>
                <div style="font-size:11px;color:#9ca3af;display:flex;gap:14px;">
                    <span><span style="display:inline-block;width:12px;height:3px;background:#667eea;border-radius:10px;vertical-align:middle;margin-right:4px;"></span>Visitors</span>
                    <span><span style="display:inline-block;width:12px;height:0;border-top:2px dashed #06b6d4;vertical-align:middle;margin-right:4px;"></span>Page Views</span>
                </div>
            </div>
            <div class="card-body" id="ga-trend">
                <div style="height:280px;background:#f9fafb;border-radius:8px;overflow:hidden;position:relative;">
                    <div style="position:absolute;inset:0;display:flex;flex-direction:column;justify-content:flex-end;padding:16px;gap:6px;">
                        @for($i = 0; $i < 5; $i++)
                            <span class="skel-line" style="width:100%;height:{{ 40 + $i*10 }}px;opacity:{{ 0.4 + $i*0.12 }};"></span>
                            @endfor
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ══════════════════════════════════════════════
         SECTION 3 — SOURCES + DEVICES
    ══════════════════════════════════════════════ --}}
<div class="row g-3 mb-3">
    <div class="col-xl-6 col-md-6 col-12">
        <div class="card h-100 ga-card-wrap" id="wrap-sources">
            <div class="ga-loading-bar"></div>
            <div id="ga-sources">
                <div class="p-3">
                    <span class="skel-line" style="width:50%;height:14px;"></span>
                    @for($i = 0; $i < 4; $i++)
                        <div class="mt-3">
                        <span class="skel-line" style="width:70%;height:11px;"></span>
                        <span class="skel-line" style="width:100%;height:7px;"></span>
                </div>
                @endfor
            </div>
        </div>
    </div>
</div>
<div class="col-xl-6 col-md-6 col-12">
    <div class="card h-100 ga-card-wrap" id="wrap-devices">
        <div class="ga-loading-bar"></div>
        <div id="ga-devices">
            <div class="p-3">
                <span class="skel-line" style="width:40%;height:14px;"></span>
                <span class="skel-line" style="width:30%;height:26px;margin-top:10px;"></span>
                @for($i = 0; $i < 3; $i++)
                    <div class="mt-3">
                    <span class="skel-line" style="width:60%;height:11px;"></span>
                    <span class="skel-line" style="width:100%;height:7px;"></span>
            </div>
            @endfor
        </div>
    </div>
</div>
</div>
</div>
<div class="row mb-3">
    <div class="col-md-6 mb-3 mb-md-0">
        <div class="card ga-card-wrap h-100" id="wrap-toppages">
            <div class="ga-loading-bar"></div>
            <div class="card-header">
                <h4 class="card-title mb-0">
                    Top Pages
                </h4>
            </div>
            <div class="card-body" id="ga-toppages">
                <div class="p-2">
                    @for($i = 0; $i < 5; $i++)
                        <div class="d-flex gap-2 align-items-center mb-3">
                        <span class="skel-line" style="width:22px;height:22px;border-radius:6px;flex-shrink:0;"></span>
                        <div class="flex-fill">
                            <span class="skel-line" style="width:{{ 55 + $i*5 }}%;height:12px;"></span>
                            <span class="skel-line" style="width:30%;height:10px;"></span>
                        </div>
                </div>
                @endfor
            </div>
        </div>
    </div>
</div>
<div class="col-md-6">
    <div class="card ga-card-wrap h-100" id="wrap-countries">
        <div class="ga-loading-bar"></div>
        <div class="card-header">
            <h4 class="card-title mb-0">
                Top Countries
            </h4>
        </div>
        <div class="card-body" id="ga-countries">
            <div class="p-2">
                @for($i = 0; $i < 5; $i++)
                    <div class="d-flex gap-2 align-items-center mb-3">
                    <span class="skel-line" style="width:28px;height:20px;border-radius:4px;flex-shrink:0;"></span>
                    <div class="flex-fill">
                        <span class="skel-line" style="width:{{ 45 + $i*8 }}%;height:12px;"></span>
                        <span class="skel-line" style="width:100%;height:6px;"></span>
                    </div>
                    <span class="skel-line" style="width:32px;height:12px;flex-shrink:0;"></span>
            </div>
            @endfor
        </div>
    </div>
</div>
</div>
</div>

</div>{{-- /.content --}}
@endsection

@push('scripts')
<script src="{{ asset('backend/assets/plugins/apexchart/apexcharts.min.js') }}" type="text/javascript"></script>

{{-- Member analytics (original) --}}
<script>
    window.memberAnalyticsUrl = "{{ route('member.analytics') }}";
</script>
<script src="{{ asset('backend/assets/js/pages/member-analytics.js') }}?v={{ config('app.assets_version') }}"></script>

{{-- GA config --}}
<script>
    window.GA_ROUTES = {
        summary: "{{ route('admin.ga.summary') }}",
        trend: "{{ route('admin.ga.trend') }}",
        sources: "{{ route('admin.ga.sources') }}",
        engagement: "{{ route('admin.ga.engagement') }}",
        devices: "{{ route('admin.ga.devices') }}",
        toppages: "{{ route('admin.ga.top-pages') }}",
        referrers: "{{ route('admin.ga.referrers') }}",
        countries: "{{ route('admin.ga.countries') }}",
    };
    window.GA_CSRF = "{{ csrf_token() }}";
</script>

<script src="{{ asset('backend/assets/js/pages/ga-dashboard.js') }}?v={{ config('app.assets_version') }}"></script>
@endpush