<?php
namespace Modules\Core\Http\Controllers\Client;
use Modules\Core\Services\EmailService;

use App\Http\Controllers\Controller;
use App\Models\Site;
use App\Models\SitePage;
use App\Models\SiteConnection;
use App\Models\PagePrice;
use App\Models\Campaign;
use App\Models\CampaignLink;
use Modules\Core\Helpers\AuthHelper;
use App\Models\BannedDomain;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CatalogController extends Controller
{
    public function index(Request $request)
    {
        $client  = AuthHelper::client();
        $balance = (float)($client->wallet?->balance ?? 0);

        if ($balance <= 0) {
            return view('client.catalog.index', ['sites' => null, 'niches' => collect(), 'balance' => 0]);
        }

        $query = Site::where('status', 'active')
            ->where('visibility', 'public');

        if ($request->filled('search')) {
            $query->where('domain', 'like', '%'.$request->search.'%');
        }
        if ($request->filled('niche')) {
            $query->where('niche', $request->niche);
        }
        if ($request->filled('min_dr')) {
            $query->where('dr', '>=', $request->min_dr);
        }
        if ($request->filled('platform')) {
            $query->where('platform_type', $request->platform);
        }
        if ($request->filled('service')) {
            if ($request->service === 'articles') {
                $query->whereIn('content_type', ['article', 'both']);
            } elseif ($request->service === 'writing') {
                $query->whereHas('unifiedUser.webmasterProfile', function($q) {
                    $q->whereJsonContains('services', 'write')
                      ->orWhereJsonContains('services', 'write_and_place');
                });
            } elseif ($request->service === 'social') {
                $query->where('platform_type', '!=', 'website');
            }
        }

        $sites = $query->with('unifiedUser.webmasterProfile')->orderByDesc('dr')->paginate(20)->withQueryString();

        // Підтягуємо pages_count з site_connections (вже кешовано там)
        $connections = \App\Models\SiteConnection::whereIn('site_id', $sites->pluck('id'))
            ->pluck('pages_count', 'site_id');
        $sites->each(fn($site) => $site->pages_count = $connections[$site->id] ?? 0);

        // Load article prices when filtering by articles service
        $articlePrices = collect();
        if ($request->service === 'articles') {
            $articlePrices = PagePrice::whereIn('site_id', $sites->pluck('id'))
                ->where('price_type', 'article_client')
                ->where('scope_type', 'site_default')
                ->get()
                ->keyBy('site_id');
        }

        $niches = Site::where('status', 'active')->where('visibility', 'public')
            ->distinct()->pluck('niche')->filter()->sort()->values();

        $platforms = ['website', 'facebook', 'instagram', 'tiktok', 'linkedin', 'telegram', 'youtube', 'twitter'];
        return view('client.catalog.index', compact('sites', 'niches', 'balance', 'platforms', 'articlePrices'));
    }

    public function show(Request $request, Site $site)
    {
        if ($site->status !== 'active' || $site->visibility !== 'public') {
            abort(404);
        }

        $tab    = $request->get('tab', 'link');
        $search = $request->get('search', '');

        // Знаходимо connection для цього сайту
        $connection = SiteConnection::where('site_id', $site->id)->first();

        $pages = collect();
        $paginator = null;

        if ($connection) {
            $query = SitePage::where('site_id', $connection->id)
                ->where('status', 'publish');

            if ($search) {
                $query->where(function($q) use ($search) {
                    $q->where('url', 'like', '%'.$search.'%')
                      ->orWhere('title', 'like', '%'.$search.'%');
                });
            }

            $paginator = $query->orderByDesc('published_at')
                ->paginate(20)
                ->withQueryString();

            // Підтягуємо ціни для кожної сторінки
            $client = AuthHelper::client();
            $clientId = $client?->id;
            $paginator->getCollection()->transform(function($page) use ($site, $tab, $clientId) {
                $price = PagePrice::resolveForUrl($site->id, $page->url, $clientId, $tab);
                $page->resolved_price = $price ? $price->getPriceForType($tab) : null;
                return $page;
            });
        }

        // Ціни для статей (site_default)
        $articleClientPrice    = PagePrice::where('site_id', $site->id)
            ->where('price_type', 'article_client')
            ->where('scope_type', 'site_default')
            ->first();
        $articleWebmasterPrice = PagePrice::where('site_id', $site->id)
            ->where('price_type', 'article_webmaster')
            ->where('scope_type', 'site_default')
            ->first();

        return view('client.catalog.show', compact(
            'site', 'tab', 'search', 'paginator',
            'articleClientPrice', 'articleWebmasterPrice'
        ));
    }

    public function order(Request $request, Site $site)
    {
        $client = AuthHelper::client();
        $wallet = $client->wallet;

        $data = $request->validate([
            'placement_type' => 'required|in:link,onclick,article_once,article_daily',
            'donor_url'      => 'required_if:placement_type,link,onclick|nullable|url',
            'target_url'     => 'required_if:placement_type,link|nullable|url',
            'anchor'         => 'required_if:placement_type,link|nullable|string|max:255',
            'anchor_before'  => 'nullable|string|max:255',
            'anchor_after'   => 'nullable|string|max:255',
            'onclick_href'   => 'required_if:placement_type,onclick|nullable|url',
            'article_topic'  => 'required_if:placement_type,article_once,article_daily|nullable|string|max:500',
            'link_type'      => 'nullable|in:dofollow,nofollow',
            'order_type'     => 'nullable|in:place_only,write_only,write_and_place',
        ]);

        // Перевіряємо banned domains
        if (!empty($data['donor_url'])) {
            if (BannedDomain::isBanned($data['donor_url'])) {
                return back()->with('error', __('client.err_domain_banned'));
            }
        }

        // Визначаємо ціну
        $price = null;
        $pricePerClick = null;
        if ($data['placement_type'] === 'link') {
            $pagePrice = PagePrice::resolveForUrl($site->id, $data['donor_url'], $client->id);
            $price = $pagePrice?->getPriceForType('link');
        } elseif ($data['placement_type'] === 'onclick') {
            $pagePrice = PagePrice::resolveForUrl($site->id, $data['donor_url'], $client->id);
            $pricePerClick = $pagePrice?->getPriceForType('onclick');
            $price = 0; // No upfront charge for onclick
        } elseif ($data['placement_type'] === 'article_once') {
            $pagePrice = PagePrice::where('site_id', $site->id)
                ->where('price_type', 'article_client')
                ->where('scope_type', 'site_default')->first();
            $price = $pagePrice?->price_article_once;
        } elseif ($data['placement_type'] === 'article_daily') {
            $pagePrice = PagePrice::where('site_id', $site->id)
                ->where('price_type', 'article_webmaster')
                ->where('scope_type', 'site_default')->first();
            $price = $pagePrice?->price_article_per_day;
        }

        if (!$price) {
            return back()->with('error', __('client.err_no_price'));
        }

        // Перевіряємо max_placements
        if (in_array($data['placement_type'], ['link', 'onclick'])) {
            $donorUrl = $data['donor_url'];

            // Для onclick — завжди максимум 1
            if ($data['placement_type'] === 'onclick') {
                $existing = CampaignLink::where('donor_url', $donorUrl)
                    ->where('placement_type', 'onclick')
                    ->whereIn('status', ['active', 'pending'])
                    ->count();
                if ($existing >= 1) {
                    return back()->with('error', __('client.err_onclick_exists'));
                }
            } else {
                // Для посилань — перевіряємо max_placements з page_prices
                $pagePrice = PagePrice::resolveForUrl($site->id, $donorUrl, $client->id, 'link');
                $maxPlacements = $pagePrice?->max_placements ?? 5;

                $existing = CampaignLink::where('donor_url', $donorUrl)
                    ->where('placement_type', 'link')
                    ->whereIn('status', ['active', 'pending'])
                    ->count();

                if ($existing >= $maxPlacements) {
                    return back()->with('error', __('client.err_max_placements', ['max' => $maxPlacements]));
                }
            }
        }

        if ($data['placement_type'] !== 'onclick' && (!$wallet || $wallet->balance < $price)) {
            return back()->with('error', __('client.err_insufficient_funds'));
        }
        if ($data['placement_type'] === 'onclick' && !$pricePerClick) {
            return back()->with('error', __('client.err_no_price'));
        }

        // Знаходимо або створюємо кампанію
        $campaign = Campaign::firstOrCreate(
            ['user_id' => $client->id, 'client_id' => $client->id, 'status' => 'active'],
            ['name' => __('client.default_campaign_name'), 'status' => 'active']
        );

        // Створюємо campaign_link
        CampaignLink::create([
            'campaign_id'    => $campaign->id,
            'site_id'        => $site->id,
            'donor_url'      => $data['donor_url'] ?? null,
            'target_url'     => $data['target_url'] ?? null,
            'anchor'         => $data['anchor'] ?? null,
            'anchor_before'  => $data['anchor_before'] ?? null,
            'anchor_after'   => $data['anchor_after'] ?? null,
            'onclick_href'   => $data['onclick_href'] ?? null,
            'placement_type' => $data['placement_type'],
            'order_type'     => $data['order_type'] ?? 'place_only',
            'link_type'      => $data['link_type'] ?? 'dofollow',
            'price_per_day'  => $data['placement_type'] !== 'onclick' ? $price : null,
            'price_per_click'=> $data['placement_type'] === 'onclick' ? $pricePerClick : null,
            'started_at'     => now(),
        ]);

        // Email вебмастеру про нове замовлення
        $webmaster = $site->unifiedUser;
        if ($webmaster) {
            $locale = $webmaster->locale ?? 'uk';
            EmailService::send(
                'order_created', 'webmaster', $webmaster->email,
                trans('emails.order_created_subject', [], $locale),
                'emails.order_created',
                ['domain' => $site->domain, 'placementType' => $data['placement_type'], 'pricePerDay' => $price, 'locale' => $locale]
            );
        }
        // Списуємо з гаманця (тільки для не-onclick)
        if ($data['placement_type'] !== 'onclick' && $price > 0) {
            $newBalance = $wallet->balance - $price;
            $wallet->update(['balance' => $newBalance]);
            \App\Models\WalletTransaction::create([
                'wallet_id'     => $wallet->id,
                'amount'        => -$price,
                'balance_after' => $newBalance,
                'type'          => 'charge',
                'description'   => 'Placement on ' . $site->domain,
            ]);
        }

        return redirect()->route('client.campaigns.index')
            ->with('success', __('client.order_success'));
    }
}
