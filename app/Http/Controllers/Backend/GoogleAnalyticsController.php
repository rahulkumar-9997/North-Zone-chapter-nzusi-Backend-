<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\GoogleAnalyticsService;
class GoogleAnalyticsController extends Controller
{
   protected GoogleAnalyticsService $ga;
 
    /* ✅ FIX: No type-hinted Analytics injection */
    public function __construct()
    {
        $this->ga = new GoogleAnalyticsService();
    }

    /* Validation helper */
    private function dates(Request $request): array
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date'   => 'required|date|after_or_equal:start_date',
        ]);
        return [$request->start_date, $request->end_date];
    }

    /* ════════════════════════════════════════
       1. SUMMARY — 4 KPI cards
    ════════════════════════════════════════ */
    public function summary(Request $request)
    {
        [$start, $end] = $this->dates($request);

        $data = cache()->remember("ga_sum_{$start}_{$end}", now()->addHour(),
            fn() => $this->ga->getSummary($start, $end)
        );

        return view('backend.pages.dashboard.ga-partials.summary', compact('data'));
    }

    /* ════════════════════════════════════════
       2. TREND — chart HTML + JSON
    ════════════════════════════════════════ */
    public function trend(Request $request)
    {
        [$start, $end] = $this->dates($request);

        $data = cache()->remember("ga_trend_{$start}_{$end}", now()->addHour(),
            fn() => $this->ga->getTrend($start, $end)
        );

        return view('backend.pages.dashboard.ga-partials.trend', compact('data'));
    }

    /* ════════════════════════════════════════
       3. SOURCES — traffic source bars HTML
    ════════════════════════════════════════ */
    public function sources(Request $request)
    {
        [$start, $end] = $this->dates($request);

        $data = cache()->remember("ga_src_{$start}_{$end}", now()->addHour(),
            fn() => $this->ga->getSources($start, $end)
        );

        return view('backend.pages.dashboard.ga-partials.sources', compact('data'));
    }

    /* ════════════════════════════════════════
       4. ENGAGEMENT — 6 metric boxes HTML
    ════════════════════════════════════════ */
    public function engagement(Request $request)
    {
        [$start, $end] = $this->dates($request);

        $data = cache()->remember("ga_eng_{$start}_{$end}", now()->addHour(),
            fn() => $this->ga->getEngagement($start, $end)
        );

        return view('backend.pages.dashboard.ga-partials.engagement', compact('data'));
    }

    /* ════════════════════════════════════════
       5. DEVICES — bars + donut chart HTML
    ════════════════════════════════════════ */
    public function devices(Request $request)
    {
        [$start, $end] = $this->dates($request);

        $data = cache()->remember("ga_dev_{$start}_{$end}", now()->addHour(),
            fn() => $this->ga->getDevices($start, $end)
        );

        return view('backend.pages.dashboard.ga-partials.devices', compact('data'));
    }

    /* ════════════════════════════════════════
       6. TOP PAGES — table HTML
    ════════════════════════════════════════ */
    public function topPages(Request $request)
    {
        [$start, $end] = $this->dates($request);

        $data = cache()->remember("ga_pages_{$start}_{$end}", now()->addHour(),
            fn() => $this->ga->getTopPages($start, $end)
        );

        return view('backend.pages.dashboard.ga-partials.top-pages', compact('data'));
    }

    /* ════════════════════════════════════════
       7. REFERRERS — referrer list HTML
    ════════════════════════════════════════ */
    public function referrers(Request $request)
    {
        [$start, $end] = $this->dates($request);

        $data = cache()->remember("ga_ref_{$start}_{$end}", now()->addHour(),
            fn() => $this->ga->getReferrers($start, $end)
        );

        return view('backend.pages.dashboard.ga-partials.referrers', compact('data'));
    }
}
