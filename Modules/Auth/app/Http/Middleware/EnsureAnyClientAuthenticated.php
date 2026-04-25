<?php
namespace Modules\Auth\Http\Middleware;
use Closure;
use Illuminate\Http\Request;
class EnsureAnyClientAuthenticated
{
    public function handle(Request $request, Closure $next)
    {
        if (auth('unified')->check()) {
            $user = auth('unified')->user();
            if ($user->hasRole('client')) {
                return $next($request);
            }
            // Performer має доступ тільки до tasks
            if ($user->hasRole('performer')) {
                $allowed = ['client.tasks.index', 'client.tasks.my', 'client.tasks.complete'];
                foreach ($allowed as $route) {
                    if ($request->routeIs($route)) {
                        return $next($request);
                    }
                }
                return redirect()->route('unified.dashboard');
            }
        }
        return redirect()->route('unified.login');
    }
}
