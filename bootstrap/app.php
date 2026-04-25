<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\RateLimiter;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        apiPrefix: 'api/v1',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'gdpr.check'     => \App\Http\Middleware\CheckGdprDeleted::class,
            'check.banned'   => \App\Http\Middleware\CheckUserBanned::class,
            'client'         => \Modules\Auth\Http\Middleware\EnsureClientAuthenticated::class,
            'webmaster'      => \Modules\Auth\Http\Middleware\EnsureWebmasterAuthenticated::class,
            'unified'        => \App\Http\Middleware\EnsureUnifiedUserAuthenticated::class,
            'any_client'     => \Modules\Auth\Http\Middleware\EnsureAnyClientAuthenticated::class,
            'any_webmaster'  => \Modules\Auth\Http\Middleware\EnsureAnyWebmasterAuthenticated::class,
            'any_performer'  => \Modules\Auth\Http\Middleware\EnsurePerformerAuthenticated::class,
        ]);
        $middleware->appendToGroup('web', \App\Http\Middleware\SetLocale::class);
        $middleware->prepend(\App\Http\Middleware\DetectTenant::class);
        $middleware->validateCsrfTokens(except: [
            '/payments/callback/*',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->report(function (\Throwable $e) {
            $service = new \App\Services\ErrorReportService();
            if ($service->shouldReport($e)) {
                $service->store($e, request());
            }
        });

        $exceptions->render(function (\Throwable $e, $request) {
            if ($request->is('admin') || $request->is('admin/*') || $request->expectsJson()) {
                return null;
            }
            // 404 — показуємо кастомну сторінку
            if ($e instanceof \Symfony\Component\HttpKernel\Exception\NotFoundHttpException) {
                return response()->view('errors.404', [], 404);
            }
            // Решта помилок — тільки якщо треба репортити
            $service = new \App\Services\ErrorReportService();
            if ($service->shouldReport($e)) {
                $uuid = optional(\App\Models\ErrorReport::where('exception_class', get_class($e))
                    ->where('url', $request->fullUrl())
                    ->latest()
                    ->first())->uuid;
                return response()->view('errors.500', ['uuid' => $uuid], 500);
            }
        });
    })
    ->booted(function () {
        RateLimiter::for('track', function ($request) {
            return Limit::perMinute(200)->by($request->query('token', $request->ip()));
        });
        RateLimiter::for('snippet', function ($request) {
            return Limit::perMinute(60)->by($request->query('token', $request->ip()));
        });
        RateLimiter::for('wp-api', function ($request) {
            return Limit::perHour(10000)->by($request->header('X-Seohands-Token', $request->ip()));
        });
    })
    ->create();
