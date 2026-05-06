<?php

namespace Modules\Core\Http\Controllers\Unified;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class OnboardingController extends Controller
{
    public function show()
    {
        $user  = auth('unified')->user();
        $roles = $user->activeRoles();

        return view('unified.onboarding', compact('user', 'roles'));
    }

    public function dismiss(Request $request)
    {
        // Зберігаємо що онбординг переглянуто
        $user = auth('unified')->user();
        $user->update(['onboarded_at' => now()]);

        $redirect = $request->input('redirect', route('unified.dashboard'));
        return redirect($redirect);
    }
}
