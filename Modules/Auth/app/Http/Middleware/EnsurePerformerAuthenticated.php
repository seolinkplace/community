<?php
namespace Modules\Auth\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsurePerformerAuthenticated
{
    public function handle(Request $request, Closure $next): mixed
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

        if (!$user->hasRole('performer')) {
            abort(403, __('auth.forbidden'));
        }

        return $next($request);
    }
}
