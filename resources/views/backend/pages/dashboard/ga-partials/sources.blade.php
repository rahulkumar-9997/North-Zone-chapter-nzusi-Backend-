@php
$colorMap = [
'organic' => '#667eea',
'direct' => '#06b6d4',
'social' => '#10b981',
'referral' => '#f59e0b',
'paid' => '#ec4899',
'email' => '#8b5cf6',
'default' => '#9ca3af',
];
@endphp

<style>
    .src-card-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 14px;
        padding: 16px 18px 0;
    }

    .src-card-head h6 {
        font-size: 13px;
        font-weight: 700;
        color: #1f2937;
        margin: 0;
    }

    .src-chip {
        font-size: 10px;
        font-weight: 700;
        padding: 2px 9px;
        border-radius: 20px;
        background: #ede9fe;
        color: #5b21b6;
    }

    .src-body {
        padding: 0 18px 18px;
    }

    .src-big {
        font-size: 22px;
        font-weight: 800;
        color: #111827;
        line-height: 1;
        margin-bottom: 2px;
    }

    .src-sub {
        font-size: 11px;
        color: #9ca3af;
        margin-bottom: 14px;
    }

    .src-rows {
        display: flex;
        flex-direction: column;
        gap: 10px;
    }

    .src-row-lbl {
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-size: 12px;
        color: #374151;
        margin-bottom: 4px;
    }

    .src-row-lbl strong {
        font-weight: 700;
    }

    .src-row-lbl small {
        color: #9ca3af;
        margin-left: 4px;
    }

    .src-track {
        height: 6px;
        background: #f3f4f6;
        border-radius: 10px;
        overflow: hidden;
    }

    .src-fill {
        height: 100%;
        border-radius: 10px;
        transition: width .5s ease;
    }
</style>

<div class="src-card-head">
    <h4> Traffic Sources</h4>
</div>
<div class="src-body">
    <div class="src-big">{{ number_format($data['total_sessions'] ?? 0) }}</div>
    <div class="src-sub">Total sessions in this period</div>

    <div class="src-rows">
        @forelse($data['sources'] ?? [] as $src)
        @php $color = $colorMap[$src['key']] ?? $colorMap['default']; @endphp
        <div>
            <div class="src-row-lbl">
                <span>{{ $src['label'] }}</span>
                <span>
                    <strong>{{ $src['pct'] }}%</strong>
                    <small>{{ number_format($src['sessions']) }}</small>
                </span>
            </div>
            <div class="src-track">
                <div class="src-fill" style="width:{{ $src['pct'] }}%; background:{{ $color }};"></div>
            </div>
        </div>
        @empty
        <p class="text-muted" style="font-size:12px;padding:8px 0;">Is period mein koi data nahi mila.</p>
        @endforelse
    </div>
</div>