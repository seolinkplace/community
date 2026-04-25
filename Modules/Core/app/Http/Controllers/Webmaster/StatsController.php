<?php
namespace Modules\Core\Http\Controllers\Webmaster;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class StatsController extends Controller
{
    private const CACHE_TTL = 300; // 5 minutes

    public function index(Request $request)
    {
        $webmaster = \App\Helpers\AuthHelper::webmaster();
        if (!$webmaster) {
            return redirect()->route('unified.login');
        }

        $days    = in_array((int)$request->get('days', 30), [7, 14, 30, 90]) ? (int)$request->get('days', 30) : 30;
        $from    = now()->subDays($days)->toDateString();
        $siteIds = $webmaster->sites()->pluck('id');

        $empty = [
            'totalViews'       => 0,
            'totalClicks'      => 0,
            'totalUniqueVisitors' => 0,
            'totalEarnings'    => 0,
            'avgCtr'           => 0,
            'siteStats'        => collect(),
            'topAnchors'       => collect(),
            'days'             => $days,
            'chartLabels'      => [],
            'chartImpressions' => [],
            'chartClicks'      => [],
            'chartEarnings'    => [],
        ];

        if ($siteIds->isEmpty()) {
            return view('webmaster.stats.index', $empty);
        }

        $cacheKey = "wm_stats:v2:{$webmaster->id}:{$days}";

        $data = Cache::remember($cacheKey, self::CACHE_TTL, function () use ($siteIds, $webmaster, $from, $days) {

            $statsBase = DB::table('campaign_link_stats')
                ->join('campaign_links', 'campaign_link_stats.campaign_link_id', '=', 'campaign_links.id')
                ->whereIn('campaign_links.site_id', $siteIds)
                ->where('campaign_link_stats.date', '>=', $from);

            $totalViews          = (clone $statsBase)->sum('campaign_link_stats.impressions');
            $totalClicks         = (clone $statsBase)->sum('campaign_link_stats.clicks');
            $totalUniqueVisitors = (clone $statsBase)->sum('campaign_link_stats.unique_visitors');
            $avgCtr              = $totalViews > 0 ? round($totalClicks / $totalViews * 100, 2) : 0;

            // Заробіток за період
            $wmWallet      = $webmaster->wallet;
            $totalEarnings = $wmWallet
                ? DB::table('webmaster_transactions')
                    ->where('webmaster_wallet_id', $wmWallet->id)
                    ->where('type', 'earning')
                    ->where('created_at', '>=', now()->subDays($days)->startOfDay())
                    ->sum('amount')
                : 0;

            // По сайтах
            $siteStats = (clone $statsBase)
                ->join('sites', 'campaign_links.site_id', '=', 'sites.id')
                ->select(
                    'sites.id as site_id',
                    'sites.domain',
                    DB::raw('SUM(campaign_link_stats.impressions) as views'),
                    DB::raw('SUM(campaign_link_stats.clicks) as clicks'),
                    DB::raw('SUM(campaign_link_stats.unique_visitors) as unique_visitors'),
                    DB::raw('COUNT(DISTINCT campaign_links.id) as links_count')
                )
                ->groupBy('sites.id', 'sites.domain')
                ->orderByDesc('clicks')
                ->get();

            // Топ анкорів
            $topAnchors = DB::table('site_anchor_stats')
                ->join('site_connections', 'site_anchor_stats.site_id', '=', 'site_connections.id')
                ->join('sites', 'site_connections.site_id', '=', 'sites.id')
                ->where('site_connections.webmaster_id', $webmaster->id)
                ->where('site_anchor_stats.date', '>=', $from)
                ->select(
                    'sites.domain',
                    'site_anchor_stats.anchor_text',
                    'site_anchor_stats.anchor_href',
                    DB::raw('SUM(site_anchor_stats.clicks) as total_clicks')
                )
                ->groupBy('sites.domain', 'site_anchor_stats.anchor_text', 'site_anchor_stats.anchor_href')
                ->orderByDesc('total_clicks')
                ->limit(20)
                ->get();

            // Daily chart — impressions + clicks
            $chartData = (clone $statsBase)
                ->select(
                    'campaign_link_stats.date',
                    DB::raw('SUM(campaign_link_stats.impressions) as impressions'),
                    DB::raw('SUM(campaign_link_stats.clicks) as clicks')
                )
                ->groupBy('campaign_link_stats.date')
                ->orderBy('campaign_link_stats.date')
                ->get()
                ->keyBy('date');

            // Daily earnings chart
            $earningsData = $wmWallet
                ? DB::table('webmaster_transactions')
                    ->where('webmaster_wallet_id', $wmWallet->id)
                    ->where('type', 'earning')
                    ->where('created_at', '>=', now()->subDays($days - 1)->startOfDay())
                    ->select(DB::raw('DATE(created_at) as day'), DB::raw('SUM(amount) as total'))
                    ->groupBy('day')
                    ->pluck('total', 'day')
                : collect();

            $chartLabels      = [];
            $chartImpressions = [];
            $chartClicks      = [];
            $chartEarnings    = [];

            for ($i = $days - 1; $i >= 0; $i--) {
                $date               = now()->subDays($i)->toDateString();
                $label              = now()->subDays($i)->format('d.m');
                $chartLabels[]      = $label;
                $chartImpressions[] = (int)($chartData[$date]->impressions ?? 0);
                $chartClicks[]      = (int)($chartData[$date]->clicks ?? 0);
                $chartEarnings[]    = round((float)($earningsData[$date] ?? 0), 2);
            }

            return compact(
                'totalViews', 'totalClicks', 'totalUniqueVisitors', 'totalEarnings',
                'avgCtr', 'chartLabels', 'chartImpressions', 'chartClicks', 'chartEarnings'
            );
        });

        // Eloquent collections not cacheable — load outside cache
        $siteStats = DB::table('campaign_link_stats')
            ->join('campaign_links', 'campaign_link_stats.campaign_link_id', '=', 'campaign_links.id')
            ->join('sites', 'campaign_links.site_id', '=', 'sites.id')
            ->whereIn('campaign_links.site_id', $siteIds)
            ->where('campaign_link_stats.date', '>=', $from)
            ->select(
                'sites.id as site_id',
                'sites.domain',
                DB::raw('SUM(campaign_link_stats.impressions) as views'),
                DB::raw('SUM(campaign_link_stats.clicks) as clicks'),
                DB::raw('SUM(campaign_link_stats.unique_visitors) as unique_visitors'),
                DB::raw('COUNT(DISTINCT campaign_links.id) as links_count')
            )
            ->groupBy('sites.id', 'sites.domain')
            ->orderByDesc('clicks')
            ->get();

        $topAnchors = DB::table('site_anchor_stats')
            ->join('site_connections', 'site_anchor_stats.site_id', '=', 'site_connections.id')
            ->join('sites', 'site_connections.site_id', '=', 'sites.id')
            ->where('site_connections.webmaster_id', $webmaster->id)
            ->where('site_anchor_stats.date', '>=', $from)
            ->select(
                'sites.domain',
                'site_anchor_stats.anchor_text',
                'site_anchor_stats.anchor_href',
                DB::raw('SUM(site_anchor_stats.clicks) as total_clicks')
            )
            ->groupBy('sites.domain', 'site_anchor_stats.anchor_text', 'site_anchor_stats.anchor_href')
            ->orderByDesc('total_clicks')
            ->limit(20)
            ->get();

        return view('webmaster.stats.index', array_merge($data, compact('days', 'siteStats', 'topAnchors')));
    }
}
