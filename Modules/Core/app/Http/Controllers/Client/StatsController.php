<?php
namespace Modules\Core\Http\Controllers\Client;
use App\Http\Controllers\Controller;
use App\Models\CampaignLink;
use App\Models\Campaign;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\CampaignLinkStat;

class StatsController extends Controller
{
    public function index(Request $request)
    {
        $client = \App\Helpers\AuthHelper::client();
        $days   = (int) $request->get('days', 30);
        $from   = now()->subDays($days)->toDateString();

        $campaignIds = Campaign::where('client_id', $client->id)->pluck('id');

        // Активні посилання
        $activeLinks = CampaignLink::whereIn('campaign_id', $campaignIds)
            ->where('status', 'active')->count();

        // Всього посилань
        $totalLinks = CampaignLink::whereIn('campaign_id', $campaignIds)->count();

        // По статусах
        $byStatus = CampaignLink::whereIn('campaign_id', $campaignIds)
            ->select('status', DB::raw('COUNT(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status');

        // По типах розміщення
        $byType = CampaignLink::whereIn('campaign_id', $campaignIds)
            ->select('placement_type', DB::raw('COUNT(*) as count'))
            ->groupBy('placement_type')
            ->pluck('count', 'placement_type');

        // Нові за період
        $newLinks = CampaignLink::whereIn('campaign_id', $campaignIds)
            ->where('created_at', '>=', $from)
            ->count();

        // Топ сайтів де розміщено
        $topSites = CampaignLink::whereIn('campaign_id', $campaignIds)
            ->where('campaign_links.status', 'active')
            ->join('sites', 'campaign_links.site_id', '=', 'sites.id')
            ->select('sites.domain', DB::raw('COUNT(*) as links_count'))
            ->groupBy('sites.id', 'sites.domain')
            ->orderByDesc('links_count')
            ->limit(10)
            ->get();

        // Daily impressions/clicks chart data
        $linkIds = CampaignLink::whereIn('campaign_id', $campaignIds)->pluck('id');
        $chartData = CampaignLinkStat::whereIn('campaign_link_id', $linkIds)
            ->where('date', '>=', $from)
            ->select('date', DB::raw('SUM(impressions) as impressions'), DB::raw('SUM(clicks) as clicks'))
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->keyBy(fn($r) => $r->date->toDateString());

        // Fill missing dates with zeros
        $chartLabels = [];
        $chartImpressions = [];
        $chartClicks = [];
        for ($i = $days - 1; $i >= 0; $i--) {
            $date = now()->subDays($i)->toDateString();
            $chartLabels[] = $date;
            $chartImpressions[] = $chartData[$date]->impressions ?? 0;
            $chartClicks[] = $chartData[$date]->clicks ?? 0;
        }

        $pendingLinks = CampaignLink::whereIn('campaign_id', $campaignIds)
            ->where('status', 'pending')->count();

        return view('client.stats.index', compact(
            'activeLinks', 'totalLinks', 'byStatus', 'byType',
            'newLinks', 'topSites', 'days', 'pendingLinks',
            'chartLabels', 'chartImpressions', 'chartClicks'
        ));
    }
}
