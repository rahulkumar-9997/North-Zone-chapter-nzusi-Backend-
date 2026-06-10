
<div id="gaApexTrend" style="min-height:280px;"></div>
<script type="application/json" id="gaChartData">
{!! json_encode([
    'dates'     => $data['dates']     ?? [],
    'visitors'  => $data['visitors']  ?? [],
    'pageviews' => $data['pageviews'] ?? [],
]) !!}
</script>
<div style="display:flex;gap:16px;margin-top:10px;font-size:11px;color:#9ca3af;padding:0 4px;">
    <span>
        <span style="display:inline-block;width:12px;height:3px;background:#667eea;border-radius:10px;vertical-align:middle;margin-right:4px;"></span>
        Visitors
    </span>
    <span>
        <span style="display:inline-block;width:12px;height:0;border-top:2px dashed #06b6d4;vertical-align:middle;margin-right:4px;"></span>
        Page Views
    </span>
    <span style="margin-left:auto;color:#d1d5db;font-size:10px;">
        {{ count($data['dates'] ?? []) }} data points
    </span>
</div>
