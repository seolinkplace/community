<?php
namespace Modules\Core\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureUnifiedUserAuthenticated
{
    public function handle(Request $request, Closure $next, string $role = null)
    {
        if (!auth('unified')->check()) {
            return redirect()->route('unified.login');
        }

        $user = auth('unified')->user();

        if ($user->status === 'banned') {
            auth('unified')->logout();
            return redirect()->route('unified.login')
                ->withErrors(['email' => __('auth.account_banned')]);
        }

        // Якщо вказана роль — перевіряємо що вона є
        if ($role && !$user->hasRole($role)) {
            abort(403, __('auth.forbidden'));
        }

        return $next($request);
    }
}
