<?php

namespace App\Providers\Filament;

use App\Filament\Pages\SiteSettings;
use App\Filament\Widgets\LatestPostsWidget;
use App\Filament\Widgets\PostStatsWidget;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationGroup;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Support\Enums\Width;
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
            // ------------------------------------------------------------------
            // Identity
            // ------------------------------------------------------------------
            ->default()
            ->id('admin')
            ->path('admin')
            ->brandName('CMS')
            // ->brandLogo(asset('images/logo.svg'))   // uncomment + add your logo
            // ->favicon(asset('images/favicon.png'))  // uncomment for custom favicon

            // ------------------------------------------------------------------
            // Auth
            // ------------------------------------------------------------------
            ->login()
            ->profile()          // Adds a "My Profile" link in the user menu
            // ->registration()  // Uncomment to allow self-registration (use with caution)

            // ------------------------------------------------------------------
            // Colors (Tailwind palette names or hex)
            // ------------------------------------------------------------------
            ->colors([
                'primary' => Color::Blue,
                'gray'    => Color::Slate,
            ])

            // ------------------------------------------------------------------
            // UX options
            // ------------------------------------------------------------------
            ->sidebarCollapsibleOnDesktop()   // sidebar can collapse to icons
            ->spa()                            // SPA navigation (no full page reload)
            ->maxContentWidth(Width::Full)  // use full width for tables

            // ------------------------------------------------------------------
            // Global search (searches across all Resources)
            // ------------------------------------------------------------------
            ->globalSearch()
            ->globalSearchKeyBindings(['command+k', 'ctrl+k'])

            // ------------------------------------------------------------------
            // Navigation groups (controls sidebar section headings + order)
            // ------------------------------------------------------------------
            ->navigationGroups([
                NavigationGroup::make('Content')
                    ->icon('heroicon-o-document-text'),
                NavigationGroup::make('Media')
                    ->icon('heroicon-o-photo'),
                NavigationGroup::make('Users & Roles')
                    ->icon('heroicon-o-users'),
                NavigationGroup::make('System')
                    ->icon('heroicon-o-cog-6-tooth')
                    ->collapsed(),     // collapsed by default in the sidebar
            ])

            // ------------------------------------------------------------------
            // Resource / Page / Widget auto-discovery
            // ------------------------------------------------------------------
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->pages([
                Dashboard::class,
                SiteSettings::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->widgets([
                AccountWidget::class,
                FilamentInfoWidget::class,
                PostStatsWidget::class,
                LatestPostsWidget::class,
            ])

            // ------------------------------------------------------------------
            // Middleware
            // ------------------------------------------------------------------
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
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
