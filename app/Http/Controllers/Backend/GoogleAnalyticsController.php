<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Services\GoogleAnalyticsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class GoogleAnalyticsController extends Controller
{
    protected GoogleAnalyticsService $ga;

    public function __construct()
    {
        $this->ga = new GoogleAnalyticsService();
    }

    /**
     * Validate + extract dates.
     * FIX: 'after_or_equal' allows same start and end date (Today / Yesterday).
     * Also caps end_date at today — prevents future dates.
     */
    private function dates(Request $request): array
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date'   => 'required|date|after_or_equal:start_date',
        ]);

        $start = $request->start_date;
        $end   = $request->end_date;

        /* Cap end_date at today — prevents GA API from rejecting future dates */
        $today = now()->format('Y-m-d');
        if ($end > $today) {
            $end = $today;
        }

        /* If same day (Today / Yesterday), GA4 needs startDate = endDate — that's fine */
        return [$start, $end];
    }

    /**
     * Return a clean HTML error block instead of throwing — 
     * so AJAX shows error in the card, not a 500 page.
     */
    private function errorView(string $section, \Exception $e): \Illuminate\Http\Response
    {
        Log::error("GA {$section} failed", [
            'message' => $e->getMessage(),
            'file'    => $e->getFile(),
            'line'    => $e->getLine(),
        ]);

        $msg = app()->isLocal()
            ? e($e->getMessage())   // Show actual error in local/dev
            : 'Data load karne mein error aaya. Please try again.';

        return response(
            '<div class="ga-error-block">'
            . '<i class="ti ti-alert-circle"></i>'
            . '<span>' . $section . ': ' . $msg . '</span>'
            . '</div>',
            200   /* 200 so AJAX success handler receives it */
        );
    }

    /* ════════════════════════════════
       SUMMARY
    ════════════════════════════════ */
    public function summary(Request $request)
    {
        try {
            [$start, $end] = $this->dates($request);
            $data = $this->ga->getSummary($start, $end);
            return view('backend.pages.dashboard.ga-partials.summary', compact('data'));
        } catch (\Exception $e) {
            return $this->errorView('Summary', $e);
        }
    }

    /* ════════════════════════════════
       TREND
    ════════════════════════════════ */
    public function trend(Request $request)
    {
        try {
            [$start, $end] = $this->dates($request);
            $data = $this->ga->getTrend($start, $end);
            return view('backend.pages.dashboard.ga-partials.trend', compact('data'));
        } catch (\Exception $e) {
            return $this->errorView('Trend', $e);
        }
    }

    /* ════════════════════════════════
       SOURCES
    ════════════════════════════════ */
    public function sources(Request $request)
    {
        try {
            [$start, $end] = $this->dates($request);
            $data = $this->ga->getSources($start, $end);
            return view('backend.pages.dashboard.ga-partials.sources', compact('data'));
        } catch (\Exception $e) {
            return $this->errorView('Sources', $e);
        }
    }

    /* ════════════════════════════════
       ENGAGEMENT
    ════════════════════════════════ */
    public function engagement(Request $request)
    {
        try {
            [$start, $end] = $this->dates($request);
            $data = $this->ga->getEngagement($start, $end);
            return view('backend.pages.dashboard.ga-partials.engagement', compact('data'));
        } catch (\Exception $e) {
            return $this->errorView('Engagement', $e);
        }
    }

    /* ════════════════════════════════
       DEVICES
    ════════════════════════════════ */
    public function devices(Request $request)
    {
        try {
            [$start, $end] = $this->dates($request);
            $data = $this->ga->getDevices($start, $end);
            return view('backend.pages.dashboard.ga-partials.devices', compact('data'));
        } catch (\Exception $e) {
            return $this->errorView('Devices', $e);
        }
    }

    /* ════════════════════════════════
       TOP PAGES
    ════════════════════════════════ */
    public function topPages(Request $request)
    {
        try {
            [$start, $end] = $this->dates($request);
            $data = $this->ga->getTopPages($start, $end);
            return view('backend.pages.dashboard.ga-partials.top-pages', compact('data'));
        } catch (\Exception $e) {
            return $this->errorView('Top Pages', $e);
        }
    }

    /* ════════════════════════════════
       REFERRERS
    ════════════════════════════════ */
    public function referrers(Request $request)
    {
        try {
            [$start, $end] = $this->dates($request);
            $data = $this->ga->getReferrers($start, $end);
            return view('backend.pages.dashboard.ga-partials.referrers', compact('data'));
        } catch (\Exception $e) {
            return $this->errorView('Referrers', $e);
        }
    }

    /* ════════════════════════════════
       COUNTRIES
    ════════════════════════════════ */
    public function countries(Request $request)
    {
        try {
            [$start, $end] = $this->dates($request);
            $data = $this->ga->getCountries($start, $end);
            return view('backend.pages.dashboard.ga-partials.country', compact('data'));
        } catch (\Exception $e) {
            return $this->errorView('Countries', $e);
        }
    }
}