<?php
namespace Modules\Auth\Http\Middleware;
use Closure;
use Illuminate\Http\Request;
class EnsureAnyWebmasterAuthenticated
{
    public function handle(Request $request, Closure $next)
    {
        if (auth('unified')->check() && auth('unified')->user()->hasRole('webmaster')) {
            return $next($request);
        }
        return redirect()->route('unified.login');
    }
}
