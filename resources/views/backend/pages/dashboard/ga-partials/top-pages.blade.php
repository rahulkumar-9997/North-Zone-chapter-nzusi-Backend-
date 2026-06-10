<style>
.ga-pages-table { width:100%; border-collapse:collapse; }
.ga-pages-table th {
    font-size:10px; text-transform:uppercase; letter-spacing:.5px;
    color:#9ca3af; padding:8px 10px; text-align:left;
    border-bottom:1px solid #f3f4f6; font-weight:600;
}
.ga-pages-table td {
    padding:10px 10px; font-size:12.5px; color:#374151;
    border-bottom:1px solid #f9fafb;
}
.ga-pages-table tbody tr:last-child td { border-bottom:none; }
.ga-pages-table tbody tr:hover td { background:#fafbff; }

.pg-rank {
    display:inline-flex; align-items:center; justify-content:center;
    width:24px; height:24px; background:#f3f4f6;
    border-radius:6px; font-size:11px; font-weight:700; color:#6b7280;
}
.pg-page-name { font-weight:500; color:#1f2937; }
.pg-page-name small { display:block; color:#9ca3af; font-size:10px; margin-top:1px; }
.bc-g { color:#059669; font-weight:600; }
.bc-o { color:#d97706; font-weight:600; }
.bc-r { color:#dc2626; font-weight:600; }

.views-badge {
    background:#f0f9ff; color:#0369a1;
    border-radius:6px; padding:2px 8px;
    font-size:11px; font-weight:700;
}
</style>

@if(empty($data['pages']))
    <p class="text-muted text-center" style="padding:24px;font-size:13px;">
        Is period mein koi page data nahi mila.
    </p>
@else
<div class="table-responsive">
    <table class="ga-pages-table">
        <thead>
            <tr>
                <th style="width:40px;">#</th>
                <th>Page</th>
                <th>Views</th>
                <th>Avg. Time</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data['pages'] as $i => $page)
            @php
                $pageName   = $page['page'] ?? '/';
                $pageTitle  = $page['title'] ?? null;
                $shortName  = strlen($pageName) > 45 ? substr($pageName, 0, 45) . '…' : $pageName;
            @endphp
            <tr>
                <td><span class="pg-rank">{{ $i + 1 }}</span></td>
                <td>
                    <div class="pg-page-name" title="{{ $pageName }}">
                        {{ $shortName }}
                        @if($pageTitle && $pageTitle !== $pageName)
                        <small>{{ Str::limit($pageTitle, 50) }}</small>
                        @endif
                    </div>
                </td>
                <td><span class="views-badge">{{ number_format($page['views'] ?? 0) }}</span></td>                
                <td style="color:#6b7280;">{{ $page['avg_time'] ?? '–' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endif
