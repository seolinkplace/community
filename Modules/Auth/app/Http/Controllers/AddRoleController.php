<?php
namespace Modules\Auth\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\ClientProfile;
use App\Models\WebmasterProfile;
use App\Services\AffiliateService;
use Illuminate\Http\Request;

class AddRoleController extends Controller
{
    public function store(Request $request)
    {
        $user = auth('unified')->user();

        $data = $request->validate([
            'role' => ['required', 'in:client,webmaster,performer'],
        ]);

        $role = $data['role'];

        if ($user->hasRole($role)) {
            return back()->with('error', __('auth.role_already_exists'));
        }

        $user->addRole($role);

        // Refresh user in session so new role is immediately visible
        auth('unified')->setUser($user->fresh());

        // Create profile and wallet if needed
        match($role) {
            'client' => $this->setupClient($user),
            'webmaster' => $this->setupWebmaster($user),
            'performer' => $this->setupPerformer($user),
        };

        return back()->with('success', __('auth.role_added'));
    }

    private function setupClient($user): void
    {
        if (!$user->clientProfile) {
            ClientProfile::create(['user_id' => $user->id]);
        }
        if (!$user->wallet) {
            \App\Models\Wallet::create(['user_id' => $user->id, 'balance' => 0, 'reserved' => 0]);
        }
    }

    private function setupWebmaster($user): void
    {
        if (!$user->webmasterProfile) {
            WebmasterProfile::create(['user_id' => $user->id]);
        }
        if (!$user->webmasterWallet) {
            \App\Models\WebmasterWallet::create(['user_id' => $user->id, 'balance' => 0, 'pending' => 0]);
        }
    }

    private function setupPerformer($user): void
    {
        if (!$user->webmasterWallet) {
            \App\Models\WebmasterWallet::create(['user_id' => $user->id, 'balance' => 0, 'pending' => 0]);
        }
    }
}
