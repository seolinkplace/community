<?php
namespace Modules\Core\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckGdprDeleted
{
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::guard('unified')->user();
        if ($user && $user->gdpr_deleted) {
            Auth::guard('unified')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            return redirect()->route('unified.login')
                ->withErrors(['email' => __('auth.account_deleted')]);
        }
        return $next($request);
    }
}
