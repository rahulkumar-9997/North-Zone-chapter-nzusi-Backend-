<style>
    .ref-list {
        display: flex;
        flex-direction: column;
        gap: 13px;
    }

    .ref-item {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .ref-ico {
        width: 34px;
        height: 34px;
        border-radius: 9px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 12px;
        font-weight: 700;
        flex-shrink: 0;
    }

    .ref-detail {
        flex: 1;
        min-width: 0;
    }

    .ref-name {
        font-size: 13px;
        font-weight: 600;
        color: #1f2937;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .ref-bar-wrap {
        margin-top: 4px;
    }

    .ref-bar-bg {
        height: 4px;
        background: #f3f4f6;
        border-radius: 10px;
        overflow: hidden;
    }

    .ref-bar-fill {
        height: 100%;
        border-radius: 10px;
        transition: width .5s ease;
    }

    .ref-sessions {
        font-size: 12px;
        font-weight: 700;
        color: #6b7280;
        flex-shrink: 0;
        white-space: nowrap;
    }

    .ref-sessions small {
        display: block;
        font-size: 10px;
        color: #9ca3af;
        font-weight: 400;
        text-align: right;
    }
</style>

@php $maxSessions = collect($data['referrers'] ?? [])->max('sessions') ?: 1; @endphp

@if(empty($data['referrers']))
<p class="text-muted text-center" style="padding:24px;font-size:13px;">
    Is period mein koi referrer data nahi mila.
</p>
@else
<div class="ref-list">
    @foreach($data['referrers'] as $ref)
    @php
    $barWidth = round(($ref['sessions'] / $maxSessions) * 100);
    $bg = $ref['bg'] ?? '#f3f4f6';
    $color = $ref['color'] ?? '#6b7280';
    $initials = $ref['initials'] ?? strtoupper(substr($ref['source'] ?? '?', 0, 2));
    @endphp
    <div class="ref-item">
        <div class="ref-ico" style="background:{{ $bg }};color:{{ $color }};">{{ $initials }}</div>
        <div class="ref-detail">
            <div class="ref-name" title="{{ $ref['source'] ?? '' }}">{{ $ref['source'] ?? 'Unknown' }}</div>
            <div class="ref-bar-wrap">
                <div class="ref-bar-bg">
                    <div class="ref-bar-fill"
                        style="width:{{ $barWidth }}%; background:{{ $color }};"></div>
                </div>
            </div>
        </div>
        <div class="ref-sessions">
            {{ number_format($ref['sessions'] ?? 0) }}
            <small>sessions</small>
        </div>
    </div>
    @endforeach
</div>
@endif