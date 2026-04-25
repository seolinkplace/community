<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets\AccountWidget;
use Modules\Core\Filament\Widgets\StatsOverviewWidget;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->brandName(config('app.name'))
            ->colors([
                'primary' => Color::Blue,
            ])
            ->discoverResources(in: base_path('Modules/Core/app/Filament/Resources'), for: 'Modules\\Core\\Filament\\Resources')
            ->discoverResources(in: base_path('Modules/Support/app/Filament/Resources'), for: 'Modules\\Support\\Filament\\Resources')
            ->discoverResources(in: base_path('Modules/Sites/app/Filament/Resources'), for: 'Modules\\Sites\\Filament\\Resources')
            ->discoverResources(in: base_path('Modules/Blog/app/Filament/Resources'), for: 'Modules\\Blog\\Filament\\Resources')
            ->discoverPages(in: base_path('Modules/Core/app/Filament/Pages'), for: 'Modules\\Core\\Filament\\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: base_path('Modules/Core/app/Filament/Widgets'), for: 'Modules\\Core\\Filament\\Widgets')
            ->widgets([
                AccountWidget::class,
                StatsOverviewWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                PreventRequestForgery::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
