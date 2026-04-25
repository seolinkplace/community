<?php

namespace Modules\Sites\Http\Controllers\Webmaster;

use App\Http\Controllers\Controller;
use App\Models\Site;
use App\Models\TenantToken;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TokenController extends Controller
{
    public function index(Site $site)
    {
        $this->authorizeSite($site);

        $tokens = $site->tenantTokens()->with('client')->latest()->get();

        return view('webmaster.tokens.index', compact('site', 'tokens'));
    }

    public function revoke(Site $site, TenantToken $token)
    {
        $this->authorizeSite($site);

        $token->update(['status' => 'revoked']);

        return back()->with('success', __('client.token_revoked'));
    }

    public function activate(Site $site, TenantToken $token)
    {
        $this->authorizeSite($site);

        $token->update(['status' => 'active']);

        return back()->with('success', __('client.token_activated'));
    }

    private function authorizeSite(Site $site): void
    {
        if ($site->webmaster_id !== \App\Helpers\AuthHelper::webmasterId()) {
            abort(403);
        }
    }
}
