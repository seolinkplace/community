<?php

declare(strict_types=1);

namespace Modules\Sites\Http\Controllers\Webmaster;

use App\Http\Controllers\Controller;
use App\Models\Site;
use App\Models\CampaignLink;
use Modules\Core\Helpers\AuthHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SiteController extends Controller
{
    public function index()
    {
        $sites = \App\Helpers\AuthHelper::webmaster()
            ->sites()
            ->withCount(['campaignLinks', 'articles'])->with('siteLanguages')
            ->latest()
            ->paginate(20);

        return view('webmaster.sites.index', compact('sites'));
    }

    public function create()
    {
        return view('webmaster.sites.create', [
            'languages' => config('site_languages'),
            'siteLangs' => [],
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'domain'       => ['required', 'string', 'max:255', 'regex:/^([a-zA-Z0-9]([a-zA-Z0-9\-]{0,61}[a-zA-Z0-9])?\.)+[a-zA-Z]{2,}$/'],
            'niche'        => 'nullable|string|max:255',
            'language'     => 'nullable|string|max:100',
            'content_type' => 'required|in:article,link_insert,both',
            'price'        => 'nullable|numeric|min:0',
            'description'  => 'nullable|string',
            'contact'      => 'nullable|string|max:255',
            'visibility'   => 'required|in:public,private',
        ]);

        $unifiedId = auth('unified')->check() ? auth('unified')->id() : null;
        $data['user_id'] = $unifiedId ?? \App\Helpers\AuthHelper::webmasterId();
        $data['webmaster_id'] = $unifiedId ? null : \App\Helpers\AuthHelper::webmasterId();
        $data['status'] = 'active';

        $site = DB::transaction(function () use ($data, $unifiedId) {
            $site = Site::create($data);

            \App\Models\TenantToken::create([
                'site_id'      => $site->id,
                'webmaster_id' => $unifiedId ?? \App\Helpers\AuthHelper::webmasterId(),
                'client_id'    => null,
                'status'       => 'active',
                'link_limit'   => 10,
                'link_type'    => 'dofollow',
            ]);

            return $site;
        });

        return redirect()->route('webmaster.sites.tokens.index', $site->id)
            ->with('success', __('client.site_added'));
    }

    public function edit(Site $site)
    {
        $this->authorizeSite($site);
        $platformLocked = $site->pages_count > 0 || $site->wpSite()->exists();
        return view('webmaster.sites.edit', [
            'site'           => $site,
            'languages'      => config('site_languages'),
            'siteLangs'      => $site->siteLanguages()->pluck('language_code')->toArray(),
            'platformLocked' => $platformLocked,
        ]);
    }

    public function update(Request $request, Site $site)
    {
        $this->authorizeSite($site);

        $data = $request->validate([
            'domain'       => ['required', 'string', 'max:255', 'regex:/^([a-zA-Z0-9]([a-zA-Z0-9\-]{0,61}[a-zA-Z0-9])?\.)+[a-zA-Z]{2,}$/'],
            'niche'        => 'nullable|string|max:255',
            'language'     => 'nullable|string|max:100',
            'content_type' => 'required|in:article,link_insert,both',
            'price'        => 'nullable|numeric|min:0',
            'description'  => 'nullable|string',
            'contact'      => 'nullable|string|max:255',
            'visibility'   => 'required|in:public,private',
        ]);

        $site->update($data);

        $site->siteLanguages()->delete();
        foreach ($request->input("languages", []) as $code) {
            if (array_key_exists($code, config("site_languages"))) {
                $site->siteLanguages()->create(["language_code" => $code]);
            }
        }
        return redirect()->route('webmaster.sites.edit', $site)
            ->with('success', __('client.site_updated'));
    }

    public function destroy(Site $site)
    {
        $this->authorizeSite($site);
        $site->delete();

        return redirect()->route('webmaster.sites.index')
            ->with('success', __('client.site_deleted'));
    }

    public function submitFirstPost(Request $request, Site $site)
    {
        $this->authorizeSite($site);

        $request->validate([
            'first_post_url' => [
                'required',
                'url',
                'max:500',
                function (string $attribute, mixed $value, \Closure $fail) use ($site) {
                    $host = parse_url($value, PHP_URL_HOST);
                    $host = $host ? preg_replace('/^www\./', '', strtolower($host)) : '';
                    $domain = preg_replace('/^www\./', '', strtolower($site->domain));
                    if ($host !== $domain) {
                        $fail(__('validation.url_domain_mismatch'));
                    }
                },
            ],
        ]);

        $site->update([
            'first_post_url' => $request->first_post_url,
        ]);

        return redirect()->route('webmaster.dashboard')
            ->with('success', __('client.first_post_submitted'));
    }

    private function authorizeSite(Site $site): void
    {
        $userId = \App\Helpers\AuthHelper::webmasterId();
        if ($site->user_id !== $userId && $site->webmaster_id !== $userId) {
            abort(403);
        }
    }
}
