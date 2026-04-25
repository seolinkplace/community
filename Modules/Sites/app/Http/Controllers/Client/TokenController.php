<?php

namespace Modules\Sites\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\TenantToken;
use App\Models\Site;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TokenController extends Controller
{
    public function index()
    {
        $tokens = TenantToken::where('client_id', \App\Helpers\AuthHelper::clientId())
            ->with('site')
            ->latest()
            ->paginate(20);

        return view('client.tokens.index', compact('tokens'));
    }

    public function create()
    {
        return view('client.tokens.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'domain'     => 'required|string|max:255',
            'link_limit' => 'required|integer|min:1|max:100',
            'link_type'  => 'required|in:dofollow,nofollow,mixed',
        ]);

        // Знаходимо або створюємо сайт
        $site = Site::firstOrCreate(
            ['domain' => $data['domain'], 'webmaster_id' => null],
            ['status' => 'active', 'visibility' => 'private']
        );

        // Перевіряємо чи токен вже існує
        $existing = TenantToken::where('client_id', \App\Helpers\AuthHelper::clientId())
            ->where('site_id', $site->id)
            ->first();

        if ($existing) {
            return back()->withErrors(['domain' => 'Токен для цього сайту вже існує.']);
        }

        TenantToken::create([
            'client_id'  => \App\Helpers\AuthHelper::clientId(),
            'site_id'    => $site->id,
            'link_limit' => $data['link_limit'],
            'link_type'  => $data['link_type'],
            'status'     => 'active',
        ]);

        return redirect()->route('client.tokens.index')
            ->with('success', __('client.token_created'));
    }

    public function show(TenantToken $token)
    {
        $this->authorizeToken($token);
        return view('client.tokens.show', compact('token'));
    }

    public function revoke(TenantToken $token)
    {
        $this->authorizeToken($token);
        $token->update(['status' => 'revoked']);
        return back()->with('success', __('client.token_revoked'));
    }

    public function activate(TenantToken $token)
    {
        $this->authorizeToken($token);
        $token->update(['status' => 'active']);
        return back()->with('success', __('client.token_activated'));
    }

    private function authorizeToken(TenantToken $token): void
    {
        if ($token->client_id !== \App\Helpers\AuthHelper::clientId()) {
            abort(403);
        }
    }
}
