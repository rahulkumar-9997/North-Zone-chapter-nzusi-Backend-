<?php

namespace App\Services;

use Carbon\Carbon;
use Spatie\Analytics\Facades\Analytics;
use Spatie\Analytics\Period;
use Illuminate\Support\Facades\Log;
/**
 * GoogleAnalyticsService — Spatie Facade version (Fixed)
 *
 * Constructor injection band karo, Facade use karo.
 * Analytics::get() aur Analytics::runReport() directly call hota hai.
 */
class GoogleAnalyticsService
{
    protected array $refColors = [
        'google.com'    => ['bg' => '#e8f0fe', 'color' => '#1a73e8', 'initials' => 'G'],
        'facebook.com'  => ['bg' => '#e3f2fd', 'color' => '#1976d2', 'initials' => 'fb'],
        'instagram.com' => ['bg' => '#fce4ec', 'color' => '#e91e63', 'initials' => 'In'],
        'twitter.com'   => ['bg' => '#e8f5fe', 'color' => '#1da1f2', 'initials' => 'Tw'],
        'x.com'         => ['bg' => '#f1f5f9', 'color' => '#1a1f36', 'initials' => 'X'],
        'youtube.com'   => ['bg' => '#fff3e0', 'color' => '#f57c00', 'initials' => 'YT'],
        'linkedin.com'  => ['bg' => '#e1f0ff', 'color' => '#0077b5', 'initials' => 'Li'],
        'whatsapp.com'  => ['bg' => '#e8f5e9', 'color' => '#388e3c', 'initials' => 'Wh'],
        'bing.com'      => ['bg' => '#e3f2fd', 'color' => '#008272', 'initials' => 'Bn'],
    ];

    /* ══════════════════════════════════════════
       Period builder — start/end string → Period
    ══════════════════════════════════════════ */
    private function period(string $start, string $end): Period
    {
        return Period::create(
            Carbon::parse($start)->startOfDay(),
            Carbon::parse($end)->endOfDay()
        );
    }

    /* ══════════════════════════════════════════
       1. SUMMARY — visitors, pageviews, sessions, bounce
    ══════════════════════════════════════════ */
    public function getSummary(string $start, string $end): array
    {
        try {
            $period = $this->period($start, $end);

            // Spatie Facade use karo
            $rows = Analytics::get($period, [
                'sessions',
                'screenPageViews',
                'activeUsers',
                'bounceRate',
            ]);

            $row        = $rows->first() ?? [];
            $visitors   = (int)   ($row['activeUsers']      ?? 0);
            $pageviews  = (int)   ($row['screenPageViews']  ?? 0);
            $sessions   = (int)   ($row['sessions']         ?? 0);
            $bounceRate = round((float) ($row['bounceRate'] ?? 0) * 100, 1);

            return [
                'visitors'        => $visitors,
                'pageviews'       => $pageviews,
                'sessions'        => $sessions,
                'bounce_rate'     => $bounceRate,
                'visitor_change'  => 0,
                'pageview_change' => 0,
                'session_change'  => 0,
                'bounce_change'   => 0,
            ];

        } catch (\Exception $e) {
            Log::warning('[GA] getSummary: ' . $e->getMessage());
            return [
                'visitors' => 0, 'pageviews' => 0, 'sessions' => 0, 'bounce_rate' => 0,
                'visitor_change' => 0, 'pageview_change' => 0, 'session_change' => 0, 'bounce_change' => 0,
            ];
        }
    }

    /* ══════════════════════════════════════════
       2. TREND — daily visitors + pageviews for chart
    ══════════════════════════════════════════ */
    public function getTrend(string $start, string $end): array
    {
        try {
            $period = $this->period($start, $end);

            $rows = Analytics::get($period, [
                'activeUsers',
                'screenPageViews',
            ], [
                'date',
            ]);

            $dates = []; $visitors = []; $pageviews = [];

            foreach ($rows as $row) {
                // date dimension format: Ymd
                $dateStr    = $row['date'] ?? '';
                $dates[]    = $dateStr
                    ? Carbon::createFromFormat('Ymd', $dateStr)->format('d M')
                    : '';
                $visitors[]  = (int) ($row['activeUsers']     ?? 0);
                $pageviews[] = (int) ($row['screenPageViews'] ?? 0);
            }

            return compact('dates', 'visitors', 'pageviews');

        } catch (\Exception $e) {
            Log::warning('[GA] getTrend: ' . $e->getMessage());
            // Dummy trend for selected range
            $dates = []; $visitors = []; $pageviews = [];
            $d = Carbon::parse($start);
            while ($d->lte(Carbon::parse($end))) {
                $dates[]     = $d->format('d M');
                $visitors[]  = rand(80, 300);
                $pageviews[] = rand(200, 800);
                $d->addDay();
            }
            return compact('dates', 'visitors', 'pageviews');
        }
    }

    /* ══════════════════════════════════════════
       3. SOURCES — traffic channel breakdown
    ══════════════════════════════════════════ */
    public function getSources(string $start, string $end): array
    {
        try {
            $period = $this->period($start, $end);

            $rows = Analytics::get($period, [
                'sessions',
            ], [
                'sessionDefaultChannelGroup',
            ]);

            $channelMap = [
                'Organic Search' => 'organic', 'Direct'        => 'direct',
                'Organic Social' => 'social',  'Referral'      => 'referral',
                'Paid Search'    => 'paid',    'Email'         => 'email',
                'Display'        => 'display',
            ];

            $sources = [];
            foreach ($rows->sortByDesc('sessions') as $row) {
                $label     = $row['sessionDefaultChannelGroup'] ?? 'Other';
                $sessions  = (int) ($row['sessions'] ?? 0);
                $sources[] = [
                    'key'      => $channelMap[$label] ?? strtolower(str_replace(' ', '_', $label)),
                    'label'    => $label,
                    'sessions' => $sessions,
                    'pct'      => 0,
                ];
            }

            $total = array_sum(array_column($sources, 'sessions')) ?: 1;
            foreach ($sources as &$src) {
                $src['pct'] = (int) round($src['sessions'] / $total * 100);
            }

            return ['total_sessions' => $total, 'sources' => array_slice($sources, 0, 6)];

        } catch (\Exception $e) {
            Log::warning('[GA] getSources: ' . $e->getMessage());
            return [
                'total_sessions' => 0,
                'sources'        => [
                    ['key' => 'organic',  'label' => 'Organic Search', 'sessions' => 0, 'pct' => 0],
                    ['key' => 'direct',   'label' => 'Direct',         'sessions' => 0, 'pct' => 0],
                    ['key' => 'social',   'label' => 'Social',         'sessions' => 0, 'pct' => 0],
                    ['key' => 'referral', 'label' => 'Referral',       'sessions' => 0, 'pct' => 0],
                ],
            ];
        }
    }

    /* ══════════════════════════════════════════
       4. ENGAGEMENT — bounce, avg session, etc.
    ══════════════════════════════════════════ */
    public function getEngagement(string $start, string $end): array
    {
        try {
            $period = $this->period($start, $end);

            $rows = Analytics::get($period, [
                'bounceRate',
                'averageSessionDuration',
                'screenPageViewsPerSession',
                'newUsers',
                'activeUsers',
                'sessions',
            ]);

            $row        = $rows->first() ?? [];
            $totalUsers = max((int) ($row['activeUsers'] ?? 0), 1);
            $newUsers   = (int)   ($row['newUsers']     ?? 0);
            $avgSecs    = (int)   ($row['averageSessionDuration'] ?? 0);
            $bounce     = round((float) ($row['bounceRate'] ?? 0) * 100, 1);
            $pps        = round((float) ($row['screenPageViewsPerSession'] ?? 0), 1);
            $newPct     = round($newUsers / $totalUsers * 100);

            return [
                'bounce_rate'          => $bounce,
                'avg_session_duration' => sprintf('%dm %ds', intdiv($avgSecs, 60), $avgSecs % 60),
                'pages_per_session'    => $pps,
                'new_user_pct'         => $newPct,
                'returning_pct'        => 100 - $newPct,
                'sessions'             => (int) ($row['sessions'] ?? 0),
            ];

        } catch (\Exception $e) {
            Log::warning('[GA] getEngagement: ' . $e->getMessage());
            return [
                'bounce_rate'          => 0,
                'avg_session_duration' => '0m 0s',
                'pages_per_session'    => 0,
                'new_user_pct'         => 0,
                'returning_pct'        => 0,
                'sessions'             => 0,
            ];
        }
    }

    /* ══════════════════════════════════════════
       5. DEVICES — mobile / desktop / tablet %
    ══════════════════════════════════════════ */
    public function getDevices(string $start, string $end): array
    {
        try {
            $period = $this->period($start, $end);

            $rows = Analytics::get($period, [
                'sessions',
            ], [
                'deviceCategory',
            ]);

            $map = [];
            foreach ($rows as $row) {
                $map[$row['deviceCategory']] = (int) ($row['sessions'] ?? 0);
            }

            $total   = array_sum($map) ?: 1;
            $mobile  = $map['mobile']  ?? 0;
            $desktop = $map['desktop'] ?? 0;
            $tablet  = $map['tablet']  ?? 0;

            return [
                'mobile_pct'  => (int) round($mobile  / $total * 100),
                'desktop_pct' => (int) round($desktop / $total * 100),
                'tablet_pct'  => (int) round($tablet  / $total * 100),
            ];

        } catch (\Exception $e) {
            Log::warning('[GA] getDevices: ' . $e->getMessage());
            return ['mobile_pct' => 0, 'desktop_pct' => 0, 'tablet_pct' => 0];
        }
    }

    /* ══════════════════════════════════════════
       6. TOP PAGES — most visited pages
    ══════════════════════════════════════════ */
    public function getTopPages(string $start, string $end): array
    {
        try {
            $period = $this->period($start, $end);

            $rows = Analytics::get($period, [
                'screenPageViews',
                'averageSessionDuration',
                'bounceRate',
            ], [
                'pagePath',
                'pageTitle',
            ]);

            $pages = $rows
                ->sortByDesc('screenPageViews')
                ->take(10)
                ->map(function ($row) {
                    $avgSecs = (int) ($row['averageSessionDuration'] ?? 0);
                    return [
                        'page'     => $row['pagePath']  ?? '/',
                        'title'    => $row['pageTitle'] ?? null,
                        'views'    => (int)   ($row['screenPageViews']  ?? 0),
                        'bounce'   => (int)   round((float) ($row['bounceRate'] ?? 0) * 100),
                        'avg_time' => sprintf('%dm %ds', intdiv($avgSecs, 60), $avgSecs % 60),
                    ];
                })
                ->values()
                ->toArray();

            return ['pages' => $pages];

        } catch (\Exception $e) {
            Log::warning('[GA] getTopPages: ' . $e->getMessage());
            return ['pages' => []];
        }
    }

    /* ══════════════════════════════════════════
       7. REFERRERS — top referral sources
    ══════════════════════════════════════════ */
    public function getReferrers(string $start, string $end): array
    {
        try {
            $period = $this->period($start, $end);

            $rows = Analytics::get($period, [
                'sessions',
            ], [
                'sessionSource',
                'sessionMedium',
            ]);

            $refs = $rows
                ->filter(fn ($row) => ($row['sessionMedium'] ?? '') === 'referral')
                ->sortByDesc('sessions')
                ->take(8)
                ->map(function ($row) {
                    $source  = $row['sessionSource'] ?? 'unknown';
                    $palette = $this->refColors[$source] ?? [
                        'bg'      => '#f3f4f6',
                        'color'   => '#6b7280',
                        'initials'=> strtoupper(substr($source, 0, 2)),
                    ];
                    return array_merge(
                        ['source' => $source, 'sessions' => (int) ($row['sessions'] ?? 0)],
                        $palette
                    );
                })
                ->values()
                ->toArray();

            return ['referrers' => $refs];

        } catch (\Exception $e) {
            Log::warning('[GA] getReferrers: ' . $e->getMessage());
            return ['referrers' => []];
        }
    }
}