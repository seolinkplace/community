<?php
namespace Modules\Sites\Http\Controllers\Webmaster;

use App\Http\Controllers\Controller;
use App\Models\PagePrice;
use App\Models\Site;
use App\Models\SitePage;
use Illuminate\Http\Request;

class PagePriceController extends Controller
{
    private function priceIndex(Request $request, string $priceType, string $view, ?int $siteSegment = null)
    {
        $webmaster      = \App\Helpers\AuthHelper::webmaster();
        $sites          = $webmaster->sites()->get();
        $siteIds        = $sites->pluck('id');
        $selectedSiteId = $siteSegment ?: ($request->integer('site_id') ?: $sites->first()?->id);

        $prices = PagePrice::where('price_type', $priceType)
            ->whereIn('site_id', $siteIds)
            ->with(['site', 'client'])
            ->when($selectedSiteId, fn($q) => $q->where('site_id', $selectedSiteId))
            ->orderByRaw("FIELD(scope_type,'site_default','depth','url','url_client')")
            ->orderBy('scope_depth')
            ->orderBy('scope_url')
            ->paginate(30)->withQueryString();

        $depthStats = [];
        if ($selectedSiteId) {
            $connectionId = \App\Models\SiteConnection::where('site_id', $selectedSiteId)->value('id');
            $cacheKey = 'depth_stats:' . ($connectionId ?? $selectedSiteId);
            $depthStats = \Illuminate\Support\Facades\Cache::remember($cacheKey, 3600, function() use ($connectionId, $selectedSiteId) {
                return SitePage::where('site_id', $connectionId ?? $selectedSiteId)
                    ->selectRaw('
                        CASE
                            WHEN url = "/" OR url REGEXP "^https?://[^/]+/?$" THEN 0
                            ELSE LENGTH(TRIM(BOTH "/" FROM REGEXP_REPLACE(url, "^https?://[^/]+", "")))
                                 - LENGTH(REPLACE(TRIM(BOTH "/" FROM REGEXP_REPLACE(url, "^https?://[^/]+", "")), "/", ""))
                                 + 1
                        END as depth,
                        COUNT(*) as total
                    ')
                    ->groupBy('depth')
                    ->orderBy('depth')
                    ->get()
                    ->keyBy('depth')->map(fn($i) => ['depth' => $i->depth, 'total' => $i->total])->toArray();
            });
        }

        // Клієнти що мають активні замовлення на сайтах цього вебмастера
        $clientIds = \App\Models\CampaignLink::whereIn('site_id', $siteIds)
            ->join('campaigns', 'campaigns.id', '=', 'campaign_links.campaign_id')
            ->pluck('campaigns.user_id')
            ->unique();
        $clients = \App\Models\UnifiedUser::whereIn('id', $clientIds)->orderBy('email')->get();

        return view($view, compact('prices', 'sites', 'selectedSiteId', 'priceType', 'depthStats', 'clients'));
    }

    public function links(Request $request, ?int $site = null)
    {
        return $this->priceIndex($request, 'link', 'webmaster.prices.links', $site);
    }

    public function onclick(Request $request, ?int $site = null)
    {
        return $this->priceIndex($request, 'onclick', 'webmaster.prices.onclick', $site);
    }

    public function articles(Request $request, ?int $site = null)
    {
        return $this->priceIndex($request, 'article_client', 'webmaster.prices.articles', $site);
    }

    public function store(Request $request)
    {
        $webmaster = \App\Helpers\AuthHelper::webmaster();
        $siteIds   = $webmaster->sites()->pluck('id');

        $data = $request->validate([
            'site_id'        => 'required|integer|in:' . $siteIds->join(','),
            'price_type'     => 'required|in:link,onclick,article_client,article_webmaster',
            'scope_type'     => 'required|in:site_default,depth,url,url_client',
            'scope_depth'    => 'nullable|integer|min:0|max:10',
            'scope_url'      => 'nullable|url|max:500',
            'client_id'      => 'nullable|exists:unified_users,id',
            'price_per_month' => 'nullable|numeric|min:0',
            'price_per_day'  => 'nullable|numeric|min:0',
            'price_once'     => 'nullable|numeric|min:0',
            'max_placements' => 'nullable|integer|min:1',
            'is_public'      => 'boolean',
            'adult_allowed'  => 'boolean',
        ]);

        $billingType = $request->input('billing_type', 'once');
        $priceColumn = match($data['price_type']) {
            'link'               => 'price_link_per_day',
            'onclick'            => 'price_onclick_per_day',
            'article_client'     => $billingType === 'daily' ? 'price_article_per_day' : 'price_article_once',
            'article_webmaster'  => $billingType === 'daily' ? 'price_article_per_day' : 'price_article_once',
        };

        if ($data['scope_type'] === 'site_default') {
            $data['scope_depth'] = null; $data['scope_url'] = null; $data['client_id'] = null;
        } elseif ($data['scope_type'] === 'depth') {
            $data['scope_url'] = null; $data['client_id'] = null;
        } elseif ($data['scope_type'] === 'url') {
            $data['scope_depth'] = null; $data['client_id'] = null;
        } elseif ($data['scope_type'] === 'url_client') {
            $data['scope_depth'] = null;
        }

        $data['is_public']     = $request->boolean('is_public');
        $data['adult_allowed'] = $request->boolean('adult_allowed');

        $monthColumn = match($data['price_type']) {
            'link'    => 'price_link_per_month',
            'onclick' => 'price_onclick_per_month',
            default   => null,
        };

        if (!empty($data['price_per_month']) && $monthColumn) {
            $data[$monthColumn] = round((float)$data['price_per_month'], 2);
            $data[$priceColumn] = round((float)$data['price_per_month'] / 30, 6);
        } elseif (!empty($data['price_per_day'])) {
            $data[$priceColumn] = (float)$data['price_per_day'];
            if ($monthColumn) {
                $data[$monthColumn] = round((float)$data['price_per_day'] * 30, 2);
            }
        } else {
            $data[$priceColumn] = $data['price_once'] ?? null;
        }

        $data['max_placements'] = !empty($data['max_placements']) ? (int)$data['max_placements'] : 5;
        unset($data['price_per_day'], $data['price_per_month'], $data['price_once']);

        $keys = [
            'site_id'     => $data['site_id'],
            'price_type'  => $data['price_type'],
            'scope_type'  => $data['scope_type'],
            'scope_depth' => $data['scope_depth'],
            'scope_url'   => $data['scope_url'],
            'client_id'   => $data['client_id'],
        ];

        $price = PagePrice::updateOrCreate($keys, $data);

        if (request()->expectsJson()) {
            return response()->json(['ok' => true, 'id' => $price->id]);
        }

        $redirect = match($data['price_type']) {
            'link'    => 'webmaster.prices.links',
            'onclick' => 'webmaster.prices.onclick',
            default   => 'webmaster.prices.articles',
        };

        return redirect()->route($redirect, ['site' => $data['site_id']])
            ->with('success', __('client.price_saved'));
    }

    public function bulk(Request $request)
    {
        $webmaster = \App\Helpers\AuthHelper::webmaster();
        $siteIds   = $webmaster->sites()->pluck('id');

        $data = $request->validate([
            'site_id'     => 'required|integer|in:' . $siteIds->join(','),
            'price_type'  => 'required|in:link,onclick,article_client,article_webmaster',
            'scope_depth' => 'required|integer|min:0|max:10',
            'price_value' => 'required|numeric|min:0.01',
            'is_public'   => 'boolean',
        ]);

        $billingType = $request->input('billing_type', 'once');
        $priceColumn = match($data['price_type']) {
            'link'               => 'price_link_per_day',
            'onclick'            => 'price_onclick_per_day',
            'article_client'     => $billingType === 'daily' ? 'price_article_per_day' : 'price_article_once',
            'article_webmaster'  => $billingType === 'daily' ? 'price_article_per_day' : 'price_article_once',
        };

        $depth = $data['scope_depth'];

        PagePrice::updateOrCreate(
            [
                'site_id'     => $data['site_id'],
                'price_type'  => $data['price_type'],
                'scope_type'  => 'depth',
                'scope_depth' => $depth,
                'scope_url'   => null,
                'client_id'   => null,
            ],
            [
                $priceColumn    => $data['price_value'],
                'is_public'     => $request->boolean('is_public'),
                'adult_allowed' => false,
                'max_placements'=> 5,
            ]
        );

        $redirect = match($data['price_type']) {
            'link'    => 'webmaster.prices.links',
            'onclick' => 'webmaster.prices.onclick',
            default   => 'webmaster.prices.articles',
        };

        return redirect()->route($redirect, ['site' => $data['site_id']])
            ->with('success', __('client.price_set_for_depth', ['depth' => $depth]));
    }

    public function update(Request $request, PagePrice $pagePrice)
    {
        $this->authorizeOwner($pagePrice);

        $priceColumn = match($pagePrice->price_type) {
            'link'               => 'price_link_per_day',
            'onclick'            => 'price_onclick_per_day',
            'article_client'     => 'price_article_once',
            'article_webmaster'  => 'price_article_per_day',
        };

        $data = $request->validate([
            'price_value'    => 'nullable|numeric|min:0',
            'max_placements' => 'nullable|integer|min:1',
            'is_public'      => 'boolean',
            'adult_allowed'  => 'boolean',
        ]);

        $pagePrice->update([
            $priceColumn     => $data['price_value'],
            'max_placements' => $data['max_placements'] ?? null,
            'is_public'      => $request->boolean('is_public'),
            'adult_allowed'  => $request->boolean('adult_allowed'),
        ]);

        return back()->with('success', __('client.price_updated'));
    }

    public function destroy(PagePrice $pagePrice)
    {
        $this->authorizeOwner($pagePrice);
        $siteId    = $pagePrice->site_id;
        $priceType = $pagePrice->price_type;
        $pagePrice->delete();

        $redirect = match($priceType) {
            'link'    => 'webmaster.prices.links',
            'onclick' => 'webmaster.prices.onclick',
            default   => 'webmaster.prices.articles',
        };

        return redirect()->route($redirect, ['site' => $siteId])
            ->with('success', __('client.record_deleted'));
    }

    private function authorizeOwner(PagePrice $pagePrice): void
    {
        $siteIds = \App\Helpers\AuthHelper::webmaster()->sites()->pluck('id');
        if (!$siteIds->contains($pagePrice->site_id)) abort(403);
    }
}
