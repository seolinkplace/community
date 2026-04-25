<?php
namespace Modules\Core\Http\Controllers\Unified;

use App\Http\Controllers\Controller;
use App\Models\ClientProfile;
use App\Models\WebmasterProfile;
use Illuminate\Http\Request;

class GoogleRoleController extends Controller
{
    public function show()
    {
        $user = auth('unified')->user();
        // Якщо вже є роль — на dashboard
        if (!empty($user->activeRoles())) {
            return redirect()->route('unified.dashboard');
        }
        return view('unified.auth.google-role-select');
    }

    public function store(Request $request)
    {
        $user = auth('unified')->user();
        // Якщо вже є роль — на dashboard
        if (!empty($user->activeRoles())) {
            return redirect()->route('unified.dashboard');
        }

        $data = $request->validate([
            'role'     => 'required|in:client,webmaster,performer',
            'ref_code' => 'nullable|string|max:20',
        ]);

        $user->addRole($data['role']);

        match($data['role']) {
            'client'    => ClientProfile::create(['user_id' => $user->id]),
            'webmaster' => WebmasterProfile::create(['user_id' => $user->id]),
            default     => null,
        };

        if ($data['role'] === 'client') {
            \App\Models\Wallet::create(['user_id' => $user->id, 'balance' => 0, 'reserved' => 0]);
        } elseif ($data['role'] === 'webmaster' || $data['role'] === 'performer') {
            \App\Models\WebmasterWallet::create(['user_id' => $user->id, 'balance' => 0, 'pending' => 0]);
        }

        if (!empty($data['ref_code'])) {
        }

        return redirect()->route('unified.dashboard');
    }
}
