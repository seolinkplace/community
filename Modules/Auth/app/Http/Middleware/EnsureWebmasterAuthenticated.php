<?php
namespace Modules\Auth\Http\Middleware;
use Closure;
use Illuminate\Http\Request;
class EnsureWebmasterAuthenticated
{
    public function handle(Request $request, Closure $next)
    {
        if (!auth('unified')->check() || !auth('unified')->user()->hasRole('webmaster')) {
            return redirect()->route('unified.login');
        }
        return $next($request);
    }
}
