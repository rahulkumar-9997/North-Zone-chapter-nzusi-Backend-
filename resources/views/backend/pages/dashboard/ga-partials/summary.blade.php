<style>
    .ga-kpi-row {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 14px;
        margin-bottom: 4px;
    }

    @media(max-width:1100px) {
        .ga-kpi-row {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    @media(max-width:576px) {
        .ga-kpi-row {
            grid-template-columns: 1fr;
        }
    }

    .ga-kpi-card {
        background: #fff;
        border-radius: 10px;
        padding: 16px 18px;
        border: 1px solid #e5e7eb;
        box-shadow: 0 1px 3px rgba(0, 0, 0, .06);
        position: relative;
        overflow: hidden;
    }

    .ga-kpi-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 3px;
        border-radius: 10px 10px 0 0;
    }

    .ga-kpi-card.kc-indigo::before {
        background: #667eea;
    }

    .ga-kpi-card.kc-cyan::before {
        background: #06b6d4;
    }

    .ga-kpi-card.kc-green::before {
        background: #10b981;
    }

    .ga-kpi-card.kc-amber::before {
        background: #f59e0b;
    }

    .gk-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 10px;
    }

    .gk-icon {
        width: 38px;
        height: 38px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 17px;
    }

    .gi-ind {
        background: #ede9fe;
        color: #5b21b6;
    }

    .gi-cyan {
        background: #cffafe;
        color: #0e7490;
    }

    .gi-green {
        background: #d1fae5;
        color: #065f46;
    }

    .gi-amb {
        background: #fef3c7;
        color: #92400e;
    }

    .gk-badge {
        font-size: 10px;
        font-weight: 700;
        padding: 3px 8px;
        border-radius: 20px;
    }

    .gb-up {
        background: #d1fae5;
        color: #065f46;
    }

    .gb-dn {
        background: #fee2e2;
        color: #991b1b;
    }

    .gk-value {
        font-size: 26px;
        font-weight: 800;
        color: #111827;
        line-height: 1;
        margin-bottom: 4px;
    }

    .gk-label {
        font-size: 12px;
        color: #6b7280;
    }
</style>

<div class="ga-kpi-row">
    <div class="ga-kpi-card kc-indigo">
        <div class="gk-head">
            <span class="gk-icon gi-ind"><i class="ti ti-eye"></i></span>
            <span class="gk-badge {{ ($data['visitor_change'] ?? 0) >= 0 ? 'gb-up' : 'gb-dn' }}">
                {{ ($data['visitor_change'] ?? 0) >= 0 ? '+' : '' }}{{ $data['visitor_change'] ?? 0 }}%
            </span>
        </div>
        <div class="gk-value">{{ number_format($data['visitors'] ?? 0) }}</div>
        <div class="gk-label">Total Visitors</div>
    </div>

    {{-- Page Views --}}
    <div class="ga-kpi-card kc-cyan">
        <div class="gk-head">
            <span class="gk-icon gi-cyan"><i class="ti ti-browser"></i></span>
            <span class="gk-badge {{ ($data['pageview_change'] ?? 0) >= 0 ? 'gb-up' : 'gb-dn' }}">
                {{ ($data['pageview_change'] ?? 0) >= 0 ? '+' : '' }}{{ $data['pageview_change'] ?? 0 }}%
            </span>
        </div>
        <div class="gk-value">{{ number_format($data['pageviews'] ?? 0) }}</div>
        <div class="gk-label">Total Page Views</div>
    </div>

    {{-- Sessions --}}
    <div class="ga-kpi-card kc-green">
        <div class="gk-head">
            <span class="gk-icon gi-green"><i class="ti ti-refresh"></i></span>
            <span class="gk-badge {{ ($data['session_change'] ?? 0) >= 0 ? 'gb-up' : 'gb-dn' }}">
                {{ ($data['session_change'] ?? 0) >= 0 ? '+' : '' }}{{ $data['session_change'] ?? 0 }}%
            </span>
        </div>
        <div class="gk-value">{{ number_format($data['sessions'] ?? 0) }}</div>
        <div class="gk-label">Total Sessions</div>
    </div>

    {{-- Bounce Rate --}}
    <div class="ga-kpi-card kc-amber">
        <div class="gk-head">
            <span class="gk-icon gi-amb"><i class="ti ti-logout"></i></span>
            <span class="gk-badge gb-dn">
                {{ number_format($data['bounce_rate'] ?? 0, 1) }}%
            </span>
        </div>
        <div class="gk-value">{{ number_format($data['bounce_rate'] ?? 0, 1) }}%</div>
        <div class="gk-label">Bounce Rate</div>
    </div>

</div>