<?php

namespace App\Services;

use Carbon\Carbon;
use Spatie\Analytics\Facades\Analytics;
use Spatie\Analytics\Period;
use Illuminate\Support\Facades\Log;

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

    private function period(string $start, string $end): Period
    {
        return Period::create(
            Carbon::parse($start)->startOfDay(),
            Carbon::parse($end)->endOfDay()
        );
    }

    public function getSummary(string $start, string $end): array
    {
        try {
            $period = $this->period($start, $end);
            $rows = Analytics::get($period, [
                'sessions',
                'screenPageViews',
                'activeUsers',
                'bounceRate',
            ]);            
            $row = $rows->first() ?? [];
            //Log::info('[GA] Summary Final Output ' . json_encode($row, JSON_PRETTY_PRINT));
            $visitors   = (int) ($row['activeUsers'] ?? 0);
            $pageviews  = (int) ($row['screenPageViews'] ?? 0);
            $sessions   = (int) ($row['sessions'] ?? 0);
            $bounceRate = round((float) ($row['bounceRate'] ?? 0) * 100, 1);
            $result = [
                'visitors'        => $visitors,
                'pageviews'       => $pageviews,
                'sessions'        => $sessions,
                'bounce_rate'     => $bounceRate,
                'visitor_change'  => 0,
                'pageview_change' => 0,
                'session_change'  => 0,
                'bounce_change'   => 0,
            ];
            //Log::info('[GA] Summary Final Output ' . json_encode($result, JSON_PRETTY_PRINT));
            return $result;
        } catch (\Exception $e) {            
            return [
                'visitors' => 0, 'pageviews' => 0, 'sessions' => 0, 'bounce_rate' => 0,
                'visitor_change' => 0, 'pageview_change' => 0, 'session_change' => 0, 'bounce_change' => 0,
            ];
        }
    }

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
            $rows = collect($rows)->sortBy('date')->values();
            Log::info('Get trend' . json_encode($rows, JSON_PRETTY_PRINT));
            $dates = [];
            $visitors = [];
            $pageviews = [];
            foreach ($rows as $row) {
                $dateStr = $row['date'] ?? null;
                $dates[] = $dateStr ? Carbon::parse($dateStr)->format('d M') : '';
                $visitors[] = (int) ($row['activeUsers'] ?? 0);
                $pageviews[] = (int) ($row['screenPageViews'] ?? 0);
            }
            $result = compact('dates', 'visitors', 'pageviews');
            return $result;
        } catch (\Exception $e) {
            Log::warning('[GA] getTrend failed', [
                'message' => $e->getMessage(),
                'file'    => $e->getFile(),
                'line'    => $e->getLine(),
            ]);
            return [
                'dates' => [],
                'visitors' => [],
                'pageviews' => [],
            ];
        }
    }

    public function getSources(string $start, string $end): array
    {
        try {
            $period = $this->period($start, $end);
            $rows = Analytics::get(
                $period,
                ['sessions'],
                ['sessionDefaultChannelGroup']
            );
            //Log::info('get Sources' . json_encode($rows, JSON_PRETTY_PRINT));
            $channelMap = [
                'Organic Search' => 'organic',
                'Direct' => 'direct',
                'Organic Social' => 'social',
                'Referral' => 'referral',
                'Paid Search' => 'paid',
                'Email' => 'email',
                'Display' => 'display',
                'Unassigned' => 'unassigned',
            ];
            $sources = [];
            foreach ($rows->sortByDesc('sessions') as $row) {
                $label = $row['sessionDefaultChannelGroup'] ?? 'Other';
                $sessions = (int) ($row['sessions'] ?? 0);
                $sources[] = [
                    'key' => $channelMap[$label]
                        ?? strtolower(str_replace(' ', '_', $label)),
                    'label' => $label,
                    'sessions' => $sessions,
                    'pct' => 0,
                ];
            }
            $total = array_sum(array_column($sources, 'sessions'));
            if ($total > 0) {
                foreach ($sources as &$source) {
                    $source['pct'] = round(
                        ($source['sessions'] / $total) * 100,
                        1
                    );
                }
                unset($source);
            }

            return [
                'total_sessions' => $total,
                'sources' => array_slice($sources, 0, 6),
            ];

        } catch (\Exception $e) {

            Log::warning('[GA] getSources failed', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            return [
                'total_sessions' => 0,
                'sources' => [],
            ];
        }
    }

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
            //Log::info('Get Engagement' . json_encode($rows, JSON_PRETTY_PRINT));
            $row = $rows->first() ?? [];
            $activeUsers = (int) ($row['activeUsers'] ?? 0);
            $newUsers = (int) ($row['newUsers'] ?? 0);
            $sessions = (int) ($row['sessions'] ?? 0);
            $avgSeconds = (int) round((float) ($row['averageSessionDuration'] ?? 0));
            $bounceRate = round(
                ((float) ($row['bounceRate'] ?? 0)) * 100,
                1
            );
            $pagesPerSession = round(
                (float) ($row['screenPageViewsPerSession'] ?? 0),
                1
            );
            $totalUsers = max($activeUsers, 1);
            $newUserPct = round(($newUsers / $totalUsers) * 100, 1);
            $returningPct = round(100 - $newUserPct, 1);
            return [
                'bounce_rate' => $bounceRate,
                'avg_session_duration' => sprintf(
                    '%dm %ds',
                    intdiv($avgSeconds, 60),
                    $avgSeconds % 60
                ),
                'pages_per_session' => $pagesPerSession,
                'new_user_pct' => $newUserPct,
                'returning_pct' => $returningPct,
                'sessions' => $sessions,
            ];

        } catch (\Exception $e) {
            Log::warning('[GA] getEngagement failed', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            return [
                'bounce_rate' => 0,
                'avg_session_duration' => '0m 0s',
                'pages_per_session' => 0,
                'new_user_pct' => 0,
                'returning_pct' => 0,
                'sessions' => 0,
            ];
        }
    }    

    
    public function getDevices(string $start, string $end): array
    {
        try {
            $period = $this->period($start, $end);
            $rows = Analytics::get(
                $period,
                ['sessions'],
                ['deviceCategory']
            );
            //Log::info('get Devices' . json_encode($rows, JSON_PRETTY_PRINT));
            $devices = [];
            foreach ($rows as $row) {
                $device = strtolower($row['deviceCategory'] ?? 'unknown');
                $devices[$device] = (int) ($row['sessions'] ?? 0);
            }
            $mobile  = $devices['mobile'] ?? 0;
            $desktop = $devices['desktop'] ?? 0;
            $tablet  = $devices['tablet'] ?? 0;
            $total = $mobile + $desktop + $tablet;
            if ($total === 0) {
                return [
                    'mobile_pct' => 0,
                    'desktop_pct' => 0,
                    'tablet_pct' => 0,
                    'mobile_count' =>0,
                    'desktop_count' =>0,
                    'tablet_count' =>0,

                ];
            }
            return [
                'mobile_pct' => round(($mobile / $total) * 100, 1),
                'desktop_pct' => round(($desktop / $total) * 100, 1),
                'tablet_pct' => round(($tablet / $total) * 100, 1),  
                'mobile_count' =>$mobile,
                'desktop_count' =>$desktop,
                'tablet_count' =>$tablet,              
            ];

        } catch (\Exception $e) {
            Log::warning('[GA] getDevices failed', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);
            return [
                'mobile_pct' => 0,
                'desktop_pct' => 0,
                'tablet_pct' => 0, 
                'mobile_count' =>0,
                'desktop_count' =>0,
                'tablet_count' =>0,               
            ];
        }
    }

    public function getCountries(string $start, string $end): array
    {
        try {
            $period = $this->period($start, $end);
            $rows = Analytics::get(
                $period,
                ['sessions'],
                ['country']
            );
            $countries = [];
            //Log::info('get Countries' . json_encode($rows, JSON_PRETTY_PRINT));
            foreach ($rows->sortByDesc('sessions') as $row) {
                $country = $row['country'] ?? 'Unknown';
                $countries[] = [
                    'country' => $country,
                    'sessions' => (int) ($row['sessions'] ?? 0),
                ];
            }
            $totalSessions = array_sum(array_column($countries, 'sessions'));
            foreach ($countries as &$country) {
                $country['pct'] = $totalSessions > 0
                    ? round(($country['sessions'] / $totalSessions) * 100, 1)
                    : 0;
            }
            unset($country);
            return [
                'total_sessions' => $totalSessions,
                'countries' => array_slice($countries, 0, 10),
            ];
        } catch (\Exception $e) {
            Log::warning('[GA] getCountries failed', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);
            return [
                'total_sessions' => 0,
                'countries' => [],
            ];
        }
    } 

    public function getTopPages(string $start, string $end): array
    {
        try {
            $period = $this->period($start, $end);
            $rows = Analytics::get(
                $period,
                [
                    'screenPageViews',
                    'averageSessionDuration',
                ],
                [
                    'pagePath',
                    'pageTitle',
                ]
            );
            //Log::info('get Top Pages' . json_encode($rows, JSON_PRETTY_PRINT));

            $pages = $rows
                ->sortByDesc('screenPageViews')
                ->take(10)
                ->map(function ($row) {
                    $avgSeconds = (int) round(
                        (float) ($row['averageSessionDuration'] ?? 0)
                    );
                    return [
                        'page' => $row['pagePath'] ?? '/',
                        'title' => $row['pageTitle'] ?: 'Untitled Page',
                        'views' => (int) ($row['screenPageViews'] ?? 0),
                        'avg_time' => sprintf(
                            '%dm %ds',
                            intdiv($avgSeconds, 60),
                            $avgSeconds % 60
                        ),
                    ];
                })
                ->values()
                ->toArray();
            return [
                'total_pages' => count($pages),
                'pages' => $pages,
            ];
        } catch (\Exception $e) {
            Log::warning('[GA] getTopPages failed', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);
            return [
                'total_pages' => 0,
                'pages' => [],
            ];
        }
    }  
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
            Log::info('get Referrers' . json_encode($rows, JSON_PRETTY_PRINT));

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