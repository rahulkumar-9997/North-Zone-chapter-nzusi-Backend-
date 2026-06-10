<style>
    .ctr-wrap {
        padding: 4px 0;
    }

    .ctr-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 16px;
    }

    .ctr-header h6 {
        font-size: 13px;
        font-weight: 700;
        color: #1f2937;
        margin: 0;
    }

    .ctr-chip {
        font-size: 10px;
        font-weight: 700;
        padding: 2px 9px;
        border-radius: 20px;
        background: #dbeafe;
        color: #1d4ed8;
    }

    .ctr-total-box {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: #f0fdf4;
        border: 1px solid #bbf7d0;
        border-radius: 8px;
        padding: 8px 14px;
        margin-bottom: 16px;
        font-size: 12px;
        color: #065f46;
    }

    .ctr-total-box strong {
        font-size: 18px;
        font-weight: 800;
    }

    .ctr-list {
        display: flex;
        flex-direction: column;
        gap: 10px;
    }

    .ctr-item {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .ctr-flag {
        width: 32px;
        height: 24px;
        border-radius: 4px;
        background: #f3f4f6;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
        flex-shrink: 0;
        overflow: hidden;
    }

    .ctr-info {
        flex: 1;
        min-width: 0;
    }

    .ctr-name {
        font-size: 12.5px;
        font-weight: 600;
        color: #1f2937;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        margin-bottom: 3px;
    }

    .ctr-bar-wrap {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .ctr-bar-bg {
        flex: 1;
        height: 5px;
        background: #f3f4f6;
        border-radius: 10px;
        overflow: hidden;
    }

    .ctr-bar-fill {
        height: 100%;
        border-radius: 10px;
        background: #667eea;
        transition: width .5s ease;
    }

    .ctr-bar-fill.rank-1 {
        background: linear-gradient(90deg, #667eea, #764ba2);
    }

    .ctr-bar-fill.rank-2 {
        background: #06b6d4;
    }

    .ctr-bar-fill.rank-3 {
        background: #10b981;
    }

    .ctr-bar-fill.rank-4 {
        background: #f59e0b;
    }

    .ctr-bar-fill.rank-5 {
        background: #ec4899;
    }

    .ctr-pct {
        font-size: 11px;
        font-weight: 700;
        color: #6b7280;
        flex-shrink: 0;
        min-width: 36px;
        text-align: right;
    }

    .ctr-sessions {
        font-size: 11px;
        color: #9ca3af;
        flex-shrink: 0;
        min-width: 50px;
        text-align: right;
    }

    .ctr-empty {
        text-align: center;
        padding: 28px;
        color: #9ca3af;
        font-size: 13px;
    }

    /* Country rank badge */
    .ctr-rank {
        width: 22px;
        height: 22px;
        border-radius: 6px;
        background: #f3f4f6;
        color: #6b7280;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 10px;
        font-weight: 700;
        flex-shrink: 0;
    }

    .ctr-rank.top1 {
        background: #fef3c7;
        color: #d97706;
    }

    .ctr-rank.top2 {
        background: #f3f4f6;
        color: #6b7280;
    }

    .ctr-rank.top3 {
        background: #fef9c3;
        color: #ca8a04;
    }
</style>
@php
$barColors = ['rank-1','rank-2','rank-3','rank-4','rank-5'];
$topSessions = collect($data['countries'] ?? [])->max('sessions') ?: 1;
@endphp
<div class="ctr-wrap">
    @if(!empty($data['total_sessions']))
    <div class="ctr-total-box">
        <i class="ti ti-chart-pie" style="font-size:14px;"></i>
        <span>Total: <strong>{{ number_format($data['total_sessions']) }}</strong> sessions</span>
    </div>
    @endif
    @if(empty($data['countries']))
    <div class="ctr-empty">
        <i class="ti ti-world-off" style="font-size:24px;display:block;margin-bottom:8px;"></i>
        Is period mein koi country data nahi mila.
    </div>
    @else
    <div class="ctr-list">
        @foreach($data['countries'] as $i => $ctr)
        @php
        $rank = $i + 1;
        $rankClass = $rank <= 3 ? 'top' . $rank : '' ;
            $barClass=$barColors[$i] ?? 'rank-5' ;
            $barWidth=$topSessions> 0
            ? round(($ctr['sessions'] / $topSessions) * 100)
            : 0;          
            @endphp
            <div class="ctr-item">
                <div class="ctr-rank {{ $rankClass }}">{{ $rank }}</div>
                <div class="ctr-info">
                    <div class="ctr-name" title="{{ $ctr['country'] }}">{{ $ctr['country'] }}</div>
                    <div class="ctr-bar-wrap">
                        <div class="ctr-bar-bg">
                            <div class="ctr-bar-fill {{ $barClass }}" style="width:{{ $barWidth }}%;"></div>
                        </div>
                        <span class="ctr-sessions">{{ number_format($ctr['sessions']) }}</span>
                    </div>
                </div>

                {{-- Percentage --}}
                <div class="ctr-pct">{{ $ctr['pct'] }}%</div>

            </div>
            @endforeach
    </div>
    @endif

</div>