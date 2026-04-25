<?php
namespace Modules\Core\Http\Controllers\Unified;

use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    public function index()
    {
        $user  = auth('unified')->user();
        $roles = $user->activeRoles();

        // Якщо одна роль — редирект одразу
        if (count($roles) === 1) {
            return match($roles[0]) {
                'client'    => redirect()->route('unified.client.dashboard'),
                'webmaster' => redirect()->route('unified.webmaster.dashboard'),
                'performer' => redirect()->route('unified.performer.dashboard'),
                default     => view('unified.dashboard', compact('user', 'roles')),
            };
        }

        return view('unified.dashboard', compact('user', 'roles'));
    }
}
