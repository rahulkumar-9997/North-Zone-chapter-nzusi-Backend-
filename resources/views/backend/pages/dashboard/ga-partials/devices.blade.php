<style>
.dev-card-head { display:flex; align-items:center; justify-content:space-between; margin-bottom:14px; padding:16px 18px 0; }
.dev-card-head h6 { font-size:13px; font-weight:700; color:#1f2937; margin:0; }
.dev-chip-a { font-size:10px; font-weight:700; padding:2px 9px; border-radius:20px; background:#fef3c7; color:#92400e; }
.dev-body  { padding:0 18px 18px; }
.dev-big   { font-size:22px; font-weight:800; color:#111827; line-height:1; margin-bottom:2px; }
.dev-sub   { font-size:11px; color:#9ca3af; margin-bottom:14px; }
.dev-rows  { display:flex; flex-direction:column; gap:9px; margin-bottom:10px; }
.dev-lbl   { display:flex; justify-content:space-between; font-size:12px; color:#374151; margin-bottom:4px; }
.dev-track { height:6px; background:#f3f4f6; border-radius:10px; overflow:hidden; }
.dev-fill  { height:100%; border-radius:10px; transition:width .5s ease; }
</style>

<div class="dev-card-head">
    <h4>Device Breakdown</h4>
</div>
<div class="dev-body">
    <div class="dev-big">{{ $data['mobile_pct'] ?? 0 }}%</div>
    <div class="dev-sub">Mobile traffic share</div>

    <div class="dev-rows">
        <div>
            <div class="dev-lbl">
                <span><i class="ti ti-device-mobile" style="font-size:11px;margin-right:3px;"></i> Mobile {{ $data['mobile_count'] }}</span>
                <strong>{{ $data['mobile_pct'] ?? 0 }}%</strong>
            </div>
            <div class="dev-track">
                <div class="dev-fill" style="width:{{ $data['mobile_pct'] ?? 0 }}%; background:#667eea;"></div>
            </div>
        </div>
        <div>
            <div class="dev-lbl">
                <span><i class="ti ti-device-desktop" style="font-size:11px;margin-right:3px;"></i> Desktop {{ $data['desktop_count'] }}</span>
                <strong>{{ $data['desktop_pct'] ?? 0 }}%</strong>
            </div>
            <div class="dev-track">
                <div class="dev-fill" style="width:{{ $data['desktop_pct'] ?? 0 }}%; background:#10b981;"></div>
            </div>
        </div>
        <div>
            <div class="dev-lbl">
                <span><i class="ti ti-device-tablet" style="font-size:11px;margin-right:3px;"></i> Tablet {{ $data['tablet_count'] }}</span>
                <strong>{{ $data['tablet_pct'] ?? 0 }}%</strong>
            </div>
            <div class="dev-track">
                <div class="dev-fill" style="width:{{ $data['tablet_pct'] ?? 0 }}%; background:#f59e0b;"></div>
            </div>
        </div>
    </div>

    {{-- Donut chart canvas (JS renders ApexChart here) --}}
    <div id="gaApexDonut" style="margin-top:6px;"></div>
</div>

{{-- Embedded donut data — JS reads this --}}
<script type="application/json" id="gaDonutData">
{!! json_encode([
    'mobile'  => $data['mobile_pct']  ?? 0,
    'desktop' => $data['desktop_pct'] ?? 0,
    'tablet'  => $data['tablet_pct']  ?? 0,
]) !!}
</script>
