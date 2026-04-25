<?php
namespace Modules\Sites\Providers;
use Nwidart\Modules\Support\ModuleServiceProvider;
use Modules\Sites\Console\Commands\WhoisSyncCommand;

class SitesServiceProvider extends ModuleServiceProvider
{
    protected string $name = 'Sites';
    protected string $nameLower = 'sites';

    protected array $commands = [
        WhoisSyncCommand::class,
    ];

    protected array $providers = [
        EventServiceProvider::class,
        RouteServiceProvider::class,
    ];

    protected function registerAliases(): void
    {
        $aliases = [
            \App\Models\Site::class                  => \Modules\Sites\Models\Site::class,
            \App\Models\SitePage::class              => \Modules\Sites\Models\SitePage::class,
            \App\Models\SiteConnection::class        => \Modules\Sites\Models\SiteConnection::class,
            \App\Models\SiteClientPermission::class  => \Modules\Sites\Models\SiteClientPermission::class,
            \App\Models\SiteLanguage::class          => \Modules\Sites\Models\SiteLanguage::class,
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
