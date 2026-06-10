<style>
.eng-card-head { display:flex; align-items:center; justify-content:space-between; margin-bottom:14px; padding:16px 18px 0; }
.eng-card-head h6 { font-size:13px; font-weight:700; color:#1f2937; margin:0; }
.eng-chip-g { font-size:10px; font-weight:700; padding:2px 9px; border-radius:20px; background:#d1fae5; color:#065f46; }
.eng-body  { padding:0 18px 18px; }
.eng-grid  { display:grid; grid-template-columns:1fr 1fr; gap:8px; }
.eng-box   { background:#f9fafb; border-radius:8px; padding:10px 12px; }
.eng-lbl   { font-size:10px; color:#9ca3af; margin-bottom:4px; line-height:1.3; }
.eng-val   { font-size:18px; font-weight:800; line-height:1; }
</style>

<div class="eng-card-head">
    <h4>Engagement Metrics</h4>
</div>
<div class="eng-body">
    <div class="eng-grid">
        <div class="eng-box">
            <div class="eng-lbl">Bounce Rate</div>
            <div class="eng-val" style="color:#ef4444;">{{ number_format($data['bounce_rate'] ?? 0, 1) }}%</div>
        </div>
        <div class="eng-box">
            <div class="eng-lbl">Avg. Session</div>
            <div class="eng-val" style="color:#667eea;">{{ $data['avg_session_duration'] ?? '–' }}</div>
        </div>
        <div class="eng-box">
            <div class="eng-lbl">New Users</div>
            <div class="eng-val" style="color:#10b981;">{{ number_format($data['new_user_pct'] ?? 0, 0) }}%</div>
        </div>
        <div class="eng-box">
            <div class="eng-lbl">Pages / Session</div>
            <div class="eng-val" style="color:#f59e0b;">{{ number_format($data['pages_per_session'] ?? 0, 1) }}</div>
        </div>
        <div class="eng-box">
            <div class="eng-lbl">Total Sessions</div>
            <div class="eng-val" style="color:#06b6d4;">{{ number_format($data['sessions'] ?? 0) }}</div>
        </div>
        <div class="eng-box">
            <div class="eng-lbl">Returning Users</div>
            <div class="eng-val" style="color:#ec4899;">{{ number_format($data['returning_pct'] ?? 0, 0) }}%</div>
        </div>
    </div>
</div>
