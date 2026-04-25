<?php

namespace Modules\Blog\Providers;

use Nwidart\Modules\Support\ModuleServiceProvider;

class BlogServiceProvider extends ModuleServiceProvider
{
    protected string $name = 'Blog';
    protected string $nameLower = 'blog';

    protected array $providers = [
        EventServiceProvider::class,
        RouteServiceProvider::class,
    ];

    protected function registerAliases(): void
    {
        $aliases = [
            \App\Models\BlogPost::class => \Modules\Blog\Models\BlogPost::class,
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
