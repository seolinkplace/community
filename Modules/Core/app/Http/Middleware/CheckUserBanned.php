<?php
namespace Modules\Core\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckUserBanned
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth('unified')->user();

        if ($user && $user->isBanned()) {
            // Ajax/API запити
            if ($request->expectsJson()) {
                return response()->json(['message' => __('common.account_banned')], 403);
            }

            // Дозволяємо доступ до сторінки бану та апеляції
            $allowed = [
                route('unified.banned', [], false),
                route('unified.appeal.store', [], false),
            ];

            if (!in_array($request->getPathInfo(), $allowed)) {
                return redirect()->route('unified.banned');
            }
        }

        return $next($request);
    }
}
