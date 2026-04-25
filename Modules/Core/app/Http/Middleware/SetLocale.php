<?php

namespace Modules\Core\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use App\Services\LocaleService;

class SetLocale
{
    public function __construct(private LocaleService $localeService) {}

    public function handle(Request $request, Closure $next)
    {
        $locale = $this->resolveLocale($request);
        App::setLocale($locale);
        session(['locale' => $locale]);
        return $next($request);
    }

    private function resolveLocale(Request $request): string
    {
        // 1. URL prefix — найвищий пріоритет (наприклад /en/, /de/)
        $segment = $request->segment(1);
        if ($segment && $this->localeService->isSupported($segment)) {
            // Перевіряємо що це саме locale prefix а не роут типу /app/ /wm/
            $prefix = $this->localeService->prefix($segment);
            if ($prefix !== '' && $prefix === $segment) {
                return $segment;
            }
        }

        // 2. Авторизований користувач — locale з профілю
        $user = auth('unified')->user();
        if ($user?->locale && $this->localeService->isSupported($user->locale)) {
            return $user->locale;
        }

        // 3. Збережена в сесії
        $session = session('locale');
        if ($session && $this->localeService->isSupported($session)) {
            return $session;
        }

        // 4. Автодетект по Accept-Language
        return $this->localeService->detectFromRequest($request);
    }
}
