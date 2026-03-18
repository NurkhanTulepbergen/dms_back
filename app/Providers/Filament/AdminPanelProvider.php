<?php

namespace App\Providers\Filament;

use App\Http\Middleware\FilamentDebugHeaders;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets\AccountWidget;
use Filament\Widgets\FilamentInfoWidget;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
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
            ->authGuard('web')
            ->login()
            ->colors([
                'primary' => Color::Amber,
            ])
            ->discoverResources(in: base_path('Modules/Dormitory/app/Filament/Resources'), for: 'Modules\\Dormitory\\Filament\\Resources')
            ->discoverResources(in: base_path('Modules/News/app/Filament/Resources'), for: 'Modules\\News\\Filament\\Resources')
            ->discoverResources(in: base_path('Modules/User/app/Filament/Resources'), for: 'Modules\\User\\Filament\\Resources')
            ->discoverResources(in: base_path('Modules/Settlement/app/Filament/Resources'), for: 'Modules\\Settlement\\Filament\\Resources')
            ->discoverResources(in: base_path('Modules/Requests/app/Filament/Resources'), for: 'Modules\\Requests\\Filament\\Resources')
            ->discoverResources(in: base_path('Modules/Finance/app/Filament/Resources'), for: 'Modules\\Finance\\Filament\\Resources')
            ->discoverResources(in: base_path('Modules/Penalty/app/Filament/Resources'), for: 'Modules\\Penalty\\Filament\\Resources')
            ->discoverResources(in: base_path('Modules/Gym/app/Filament/Resources'), for: 'Modules\\Gym\\Filament\\Resources')




            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->widgets([
                AccountWidget::class,
                FilamentInfoWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                FilamentDebugHeaders::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
