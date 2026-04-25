<?php
namespace Modules\Core\Http\Controllers\Client;

use Modules\Core\Helpers\AuthHelper;
use App\Http\Controllers\Controller;
use App\Models\CampaignLink;
use App\Models\WalletTransaction;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    private const CACHE_TTL = 300; // 5 minutes

    public function index()
    {
        $client   = AuthHelper::client();
        $wallet   = $client->wallet;
        $cacheKey = "dashboard:client:v2:{$client->id}";

        $data = Cache::remember($cacheKey, self::CACHE_TTL, function () use ($client, $wallet) {
            $campaignIds = $client->campaigns()->pluck('id');

            $statsActive  = CampaignLink::whereIn('campaign_id', $campaignIds)->where('status', 'active')->count();
            $statsPaused  = CampaignLink::whereIn('campaign_id', $campaignIds)->where('status', 'paused')->count();
            $statsPending = CampaignLink::whereIn('campaign_id', $campaignIds)->where('status', 'pending')->count();
            $statsExpired = CampaignLink::whereIn('campaign_id', $campaignIds)->where('status', 'expired')->count();

            $spendingRaw = WalletTransaction::where('wallet_id', $wallet?->id)
                ->where('type', 'charge')
                ->where('created_at', '>=', now()->subDays(29)->startOfDay())
                ->select(DB::raw('DATE(created_at) as day'), DB::raw('SUM(ABS(amount)) as total'))
                ->groupBy('day')
                ->orderBy('day')
                ->pluck('total', 'day');

            $spendingDays = $this->fillDays($spendingRaw, 30);

            return compact(
                'statsActive', 'statsPaused', 'statsPending', 'statsExpired',
                'spendingDays'
            );
        });

        $balance      = (float)($wallet?->balance ?? 0);
        $totalSpent30 = round(array_sum($data['spendingDays']['values']), 2);
        $linkStatuses = [
            'active'  => $data['statsActive'],
            'paused'  => $data['statsPaused'],
            'pending' => $data['statsPending'],
            'expired' => $data['statsExpired'],
        ];

        // Eloquent collections not cacheable — load outside cache
        $campaignIds = $client->campaigns()->pluck('id');
        $recentLinks = CampaignLink::whereIn('campaign_id', $campaignIds)
            ->latest()
            ->limit(5)
            ->get();

        return view('client.dashboard', array_merge($data, compact(
            'wallet', 'balance', 'totalSpent30', 'linkStatuses', 'recentLinks'
        )));
    }

    private function fillDays(\Illuminate\Support\Collection $raw, int $days): array
    {
        $labels = [];
        $values = [];

        for ($i = $days - 1; $i >= 0; $i--) {
            $day      = now()->subDays($i)->format('Y-m-d');
            $labels[] = now()->subDays($i)->format('d.m');
            $values[] = round((float)($raw[$day] ?? 0), 2);
        }

        return ['labels' => $labels, 'values' => $values];
    }
}
