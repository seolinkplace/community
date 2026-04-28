<?php

namespace Modules\Core\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Site;
use App\Models\SiteConnection;
use App\Models\SitePage;
use App\Models\PagePrice;
use Modules\Core\Helpers\AuthHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

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
                $query->whereHas('unifiedUser.webmasterProfile', function ($q) {
                    $q->whereJsonContains('services', 'write')
                      ->orWhereJsonContains('services', 'write_and_place');
                });
            } elseif ($request->service === 'social') {
                $query->where('platform_type', '!=', 'website');
            }
        }

        $sites = $query->with('unifiedUser.webmasterProfile')->orderByDesc('dr')->paginate(20)->withQueryString();

        $connections = SiteConnection::whereIn('site_id', $sites->pluck('id'))
            ->pluck('pages_count', 'site_id');
        $sites->each(fn($site) => $site->pages_count = $connections[$site->id] ?? 0);

        $articlePrices = collect();
        if ($request->service === 'articles') {
            $articlePrices = PagePrice::whereIn('site_id', $sites->pluck('id'))
                ->where('price_type', 'article_client')
                ->where('scope_type', 'site_default')
                ->get()
                ->keyBy('site_id');
        }

        $niches = Cache::remember('catalog:niches', 3600, function () {
            return Site::where('status', 'active')->where('visibility', 'public')
                ->distinct()->pluck('niche')->filter()->sort()->values();
        });

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

        $connection = SiteConnection::where('site_id', $site->id)->first();

        $paginator = null;

        if ($connection) {
            $query = SitePage::where('site_id', $connection->id)
                ->where('status', 'publish');

            if ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('url', 'like', '%'.$search.'%')
                      ->orWhere('title', 'like', '%'.$search.'%');
                });
            }

            $paginator = $query->orderByDesc('published_at')
                ->paginate(20)
                ->withQueryString();

            $client   = AuthHelper::client();
            $clientId = $client?->id;
            $paginator->getCollection()->transform(function ($page) use ($site, $tab, $clientId) {
                $price = PagePrice::resolveForUrl($site->id, $page->url, $clientId, $tab);
                $page->resolved_price = $price ? $price->getPriceForType($tab) : null;
                return $page;
            });
        }

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
}
