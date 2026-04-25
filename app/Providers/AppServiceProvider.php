<?php

namespace App\Providers;

use App\Models\Client;
use App\Models\Webmaster;
use App\Models\SiteConnection;
use App\Models\SitePage;
use App\Models\SupportTicket;
use Modules\Core\Observers\ClientObserver;
use Modules\Core\Observers\WebmasterObserver;
use Modules\Sites\Observers\SiteConnectionObserver;
use Modules\Sites\Observers\SitePageObserver;
use Modules\Support\Observers\SupportTicketObserver;
use Illuminate\Support\ServiceProvider;
use Modules\Core\Services\LocaleService;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(LocaleService::class);
    }

    public function boot(): void
    {
        Client::observe(ClientObserver::class);
        Webmaster::observe(WebmasterObserver::class);
        SiteConnection::observe(SiteConnectionObserver::class);
        SitePage::observe(SitePageObserver::class);
        SupportTicket::observe(SupportTicketObserver::class);
    }
}
