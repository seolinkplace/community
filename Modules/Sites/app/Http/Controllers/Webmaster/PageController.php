<?php
namespace Modules\Sites\Http\Controllers\Webmaster;
use App\Http\Controllers\Controller;
use App\Models\Site;
use App\Models\SiteConnection;
use App\Models\SitePage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PageController extends Controller
{
    public function index(Request $request)
    {
        $webmaster = \App\Helpers\AuthHelper::webmaster();

        // Сайти вебмастера
        $sites = $webmaster->sites()->get();
        $siteIds = $sites->pluck('id');

        // Connection IDs для фільтрації site_pages
        $connectionIds = SiteConnection::whereIn('site_id', $siteIds)->pluck('id');

        if ($connectionIds->isEmpty()) {
            return view('webmaster.pages.index', [
                'pages'   => collect(),
                'wpSites' => $sites,
                'request' => $request,
            ]);
        }

        $query = SitePage::whereIn('site_id', $connectionIds);

        if ($request->filled('site_id')) {
            $site = \App\Models\Site::where('domain', $request->site_id)->first();
            $connId = $site ? SiteConnection::where('site_id', $site->id)->value('id') : null;
            if ($connId) {
                $query->where('site_id', $connId);
            } else {
                $query->whereRaw('1=0');
            }
        }

        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('title', 'like', '%'.$request->search.'%')
                  ->orWhere('url', 'like', '%'.$request->search.'%');
            });
        }

        $pages = $query->orderByDesc('published_at')->paginate(25)->withQueryString();

        return view('webmaster.pages.index', compact('pages', 'sites'))->with('wpSites', $sites);
    }

    public function show(SitePage $page)
    {
        $webmaster = \App\Helpers\AuthHelper::webmaster();
        $siteIds   = $webmaster->sites()->pluck('id');
        $connIds   = SiteConnection::whereIn('site_id', $siteIds)->pluck('id');

        if (!$connIds->contains($page->site_id)) {
            abort(403);
        }

        $anchorClicks = DB::table('site_anchor_stats')
            ->where('site_id', $page->site_id)
            ->where('date', '>=', now()->subDays(30)->toDateString())
            ->select('anchor_text', 'anchor_href', DB::raw('SUM(clicks) as total_clicks'))
            ->groupBy('anchor_text', 'anchor_href')
            ->orderByDesc('total_clicks')
            ->get()
            ->keyBy('anchor_href');

        // Знайти site_id через connection
        $siteId = $connIds->first() ? \App\Models\SiteConnection::whereIn('id', $connIds)->where('id', $page->site_id)->value('site_id') : null;

        // URL-специфічні ціни для цієї сторінки
        $urlPrices = [];
        if ($siteId) {
            $urlPrices = \App\Models\PagePrice::where('site_id', $siteId)
                ->where('scope_type', 'url')
                ->where('scope_url', $page->url)
                ->get()
                ->keyBy('price_type');
        }

        return view('webmaster.pages.show', compact('page', 'anchorClicks', 'urlPrices', 'siteId'));
    }

    public function updateLimit(Request $request, \App\Models\SitePage $page): \Illuminate\Http\JsonResponse
    {
        $request->validate(['limit' => 'required|integer|min:1|max:20']);
        $page->update(['link_limit' => $request->limit]);
        return response()->json(['ok' => true]);
    }

}
