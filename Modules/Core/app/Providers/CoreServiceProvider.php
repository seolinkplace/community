<?php

namespace Modules\Core\Providers;

use Nwidart\Modules\Support\ModuleServiceProvider;
use Modules\Core\Console\Commands\ChargeSubscriptions;

class CoreServiceProvider extends ModuleServiceProvider
{
    protected string $name = 'Core';
    protected string $nameLower = 'core';

    protected array $commands = [
        ChargeSubscriptions::class,
    ];

    protected array $providers = [
        EventServiceProvider::class,
        RouteServiceProvider::class,
    ];

    protected function registerAliases(): void
    {
        $aliases = [
            // Auth & Users
            \App\Models\UnifiedUser::class              => \Modules\Core\Models\UnifiedUser::class,
            \App\Models\UserRole::class                 => \Modules\Core\Models\UserRole::class,
            \App\Helpers\AuthHelper::class              => \Modules\Core\Helpers\AuthHelper::class,
            // Profiles
            \App\Models\ClientProfile::class            => \Modules\Core\Models\ClientProfile::class,
            \App\Models\WebmasterProfile::class         => \Modules\Core\Models\WebmasterProfile::class,
            \App\Models\PerformerProfile::class         => \Modules\Core\Models\PerformerProfile::class,
            // Legacy roles
            \App\Models\Client::class                   => \Modules\Core\Models\Client::class,
            \App\Models\Webmaster::class                => \Modules\Core\Models\Webmaster::class,
            \App\Models\User::class                     => \Modules\Core\Models\User::class,
            // User actions
            \App\Models\UserAppeal::class               => \Modules\Core\Models\UserAppeal::class,
            \App\Models\UserBlock::class                => \Modules\Core\Models\UserBlock::class,
            \App\Models\BannedDomain::class             => \Modules\Core\Models\BannedDomain::class,
            \App\Models\ErrorReport::class              => \Modules\Core\Models\ErrorReport::class,
            \App\Models\TenantToken::class              => \Modules\Core\Models\TenantToken::class,
            // Commission & Settings
            \App\Models\ApplyRequest::class             => \Modules\Core\Models\ApplyRequest::class,
            \App\Models\ClientCommissionOverride::class => \Modules\Core\Models\ClientCommissionOverride::class,
            \App\Models\CommissionSetting::class        => \Modules\Core\Models\CommissionSetting::class,
            \App\Models\SubscriptionPlan::class         => \Modules\Core\Models\SubscriptionPlan::class,
            \App\Models\UserSubscription::class         => \Modules\Core\Models\UserSubscription::class,
            \App\Models\PagePrice::class                => \Modules\Core\Models\PagePrice::class,
            \App\Models\WebmasterClientSetting::class   => \Modules\Core\Models\WebmasterClientSetting::class,
            // Middleware
            \App\Http\Middleware\CheckGdprDeleted::class               => \Modules\Core\Http\Middleware\CheckGdprDeleted::class,
            \App\Http\Middleware\CheckUserBanned::class                => \Modules\Core\Http\Middleware\CheckUserBanned::class,
            \App\Http\Middleware\EnsureUnifiedUserAuthenticated::class => \Modules\Core\Http\Middleware\EnsureUnifiedUserAuthenticated::class,
            \App\Http\Middleware\RedirectIfAuthenticated::class        => \Modules\Core\Http\Middleware\RedirectIfAuthenticated::class,
            \App\Http\Middleware\SetLocale::class                      => \Modules\Core\Http\Middleware\SetLocale::class,
            \App\Http\Middleware\DetectTenant::class                   => \Modules\Core\Http\Middleware\DetectTenant::class,
            // Services
            \App\Services\EmailService::class           => \Modules\Core\Services\EmailService::class,
            \App\Services\ErrorReportService::class     => \Modules\Core\Services\ErrorReportService::class,
            \App\Services\LocaleService::class          => \Modules\Core\Services\LocaleService::class,
            \App\Services\SubscriptionService::class    => \Modules\Core\Services\SubscriptionService::class,
            \App\Services\WpCacheFlushService::class    => \Modules\Core\Services\WpCacheFlushService::class,
            // Mail
            \App\Mail\BaseMail::class                   => \Modules\Core\Mail\BaseMail::class,
            // Helpers
            \App\Helpers\CommissionHelper::class        => \Modules\Core\Helpers\CommissionHelper::class,
            // Observers
            \App\Observers\ClientObserver::class        => \Modules\Core\Observers\ClientObserver::class,
            \App\Observers\WebmasterObserver::class     => \Modules\Core\Observers\WebmasterObserver::class,
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
