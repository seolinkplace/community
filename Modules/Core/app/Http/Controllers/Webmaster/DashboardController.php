<?php
namespace Modules\Core\Http\Controllers\Webmaster;

use App\Http\Controllers\Controller;
use Modules\Core\Helpers\AuthHelper;
use App\Models\CampaignLink;
use App\Models\TaskCompletion;
use App\Models\WebmasterTransaction;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    private const CACHE_TTL = 300; // 5 minutes

    public function index()
    {
        $webmaster = AuthHelper::webmaster();
        $wmId      = AuthHelper::webmasterId();
        $cacheKey  = "dashboard:webmaster:v2:{$wmId}";

        $data = Cache::remember($cacheKey, self::CACHE_TTL, function () use ($webmaster, $wmId) {
            $siteIds    = $webmaster->sites()->pluck('id');
            $wmWallet   = $webmaster->wallet;

            $sitesCount     = $siteIds->count();
            $activeLinks    = CampaignLink::whereIn('site_id', $siteIds)->where('status', 'active')->count();
            $pendingLinks   = CampaignLink::whereIn('site_id', $siteIds)->where('status', 'pending')->count();
            $pausedLinks    = CampaignLink::whereIn('site_id', $siteIds)->where('status', 'paused')->count();
            $expiredLinks   = CampaignLink::whereIn('site_id', $siteIds)->where('status', 'expired')->count();
            $pendingReviews = TaskCompletion::whereHas('task', fn($q) =>
                $q->where('creator_type', 'webmaster')->where('creator_id', $wmId)
            )->where('status', 'pending')->count();

            $earningsRaw = WebmasterTransaction::where('webmaster_wallet_id', $wmWallet?->id)
                ->where('type', 'earning')
                ->where('created_at', '>=', now()->subDays(29)->startOfDay())
                ->select(DB::raw('DATE(created_at) as day'), DB::raw('SUM(amount) as total'))
                ->groupBy('day')
                ->orderBy('day')
                ->pluck('total', 'day');

            $earningsDays = $this->fillDays($earningsRaw, 30);

            return compact(
                'sitesCount', 'activeLinks', 'pendingLinks', 'pausedLinks', 'expiredLinks',
                'pendingReviews', 'earningsDays'
            );
        });

        $wmWallet      = $webmaster instanceof \App\Models\UnifiedUser
            ? $webmaster->webmasterWallet
            : $webmaster->wallet;
        $balance       = (float)($wmWallet?->balance ?? 0);
        $frozen        = $wmWallet ? $wmWallet->frozenBalance() : 0.0;
        $availableForWithdrawal = $wmWallet ? $wmWallet->availableForWithdrawal() : 0.0;
        $totalEarned30 = round(array_sum($data['earningsDays']['values']), 2);
        $linkStatuses  = [
            'active'  => $data['activeLinks'],
            'paused'  => $data['pausedLinks'],
            'pending' => $data['pendingLinks'],
            'expired' => $data['expiredLinks'],
        ];

        // Eloquent collections not cacheable — load outside cache
        $siteIds = $webmaster->sites()->pluck('id');
        $firstPostGlobal             = \App\Models\Setting::get('first_post_required_global', true);
        $socialSitesWithoutFirstPost = $firstPostGlobal
            ? $webmaster->sites()->where('first_post_required', true)->where('first_post_published', false)->get()
            : collect();
        $recentLinks = CampaignLink::whereIn('site_id', $siteIds)
            ->with('campaign')
            ->latest()
            ->limit(5)
            ->get();

        return view('webmaster.dashboard', array_merge($data, compact(
            'balance', 'frozen', 'availableForWithdrawal', 'totalEarned30', 'linkStatuses',
            'recentLinks', 'socialSitesWithoutFirstPost'
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
