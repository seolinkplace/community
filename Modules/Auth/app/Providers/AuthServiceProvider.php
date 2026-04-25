<?php

namespace Modules\Auth\Providers;

use Nwidart\Modules\Support\ModuleServiceProvider;

class AuthServiceProvider extends ModuleServiceProvider
{
    protected string $name = 'Auth';
    protected string $nameLower = 'auth';

    protected array $providers = [
        EventServiceProvider::class,
        RouteServiceProvider::class,
    ];

    /**
     * Register class aliases for backward compatibility.
     * Only middleware — controllers are registered directly in routes.
     */
    protected function registerAliases(): void
    {
        $aliases = [
            \App\Http\Middleware\EnsureClientAuthenticated::class       => \Modules\Auth\Http\Middleware\EnsureClientAuthenticated::class,
            \App\Http\Middleware\EnsureWebmasterAuthenticated::class    => \Modules\Auth\Http\Middleware\EnsureWebmasterAuthenticated::class,
            \App\Http\Middleware\EnsureAnyClientAuthenticated::class    => \Modules\Auth\Http\Middleware\EnsureAnyClientAuthenticated::class,
            \App\Http\Middleware\EnsureAnyWebmasterAuthenticated::class => \Modules\Auth\Http\Middleware\EnsureAnyWebmasterAuthenticated::class,
            \App\Http\Middleware\EnsurePerformerAuthenticated::class    => \Modules\Auth\Http\Middleware\EnsurePerformerAuthenticated::class,
        ];

        foreach ($aliases as $alias => $concrete) {
            if (!class_exists($alias)) {
                class_alias($concrete, $alias);
            }
        }
    }

    public function register(): void
    {
        parent::register();
        $this->registerAliases();
    }
}
