<?php

namespace Modules\Sites\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Site;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SiteController extends Controller
{
    public function index(Request $request)
    {
        $query = Site::whereIn('id',
            \App\Models\TenantToken::where('client_id', \App\Helpers\AuthHelper::clientId())
                ->pluck('site_id')
        );

        if ($request->filled('search')) {
            $query->where('domain', 'like', '%'.$request->search.'%');
        }

        $sites = $query->withCount(['links' => function($q) {
            $q->where('client_id', \App\Helpers\AuthHelper::clientId());
        }])->paginate(20)->withQueryString();

        return view('client.sites.index', compact('sites'));
    }
}
