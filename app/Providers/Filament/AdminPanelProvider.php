<?php

namespace App\Providers\Filament;

use App\Filament\Widgets\DashboardStatsWidget;
use App\Filament\Widgets\PatientVisitsChart;
use App\Filament\Widgets\SpecialtiesChart;
use App\Http\Middleware\AdminAuthenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
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
            ->login()
            ->loginRouteSlug('giaphuoc57hv')
            ->brandName('Đa Khoa Cần Thơ CMS')
            ->sidebarCollapsibleOnDesktop()
            ->colors([
                'primary' => '#1e40af',
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                DashboardStatsWidget::class,
                PatientVisitsChart::class,
                SpecialtiesChart::class,
            ])
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
                AdminAuthenticate::class,
            ])
            ->renderHook(
                'panels::head.end',
                fn () => view('filament.scripts.open-preview')
            )
            ->renderHook(
                'panels::head.end',
                fn () => view('filament.scripts.sidebar-styles')
            )
            ->navigationGroups([
                \Filament\Navigation\NavigationGroup::make()
                    ->label('Quản lý nội dung')
                    ->icon('heroicon-o-pencil-square')
                    ->collapsed(false),
                \Filament\Navigation\NavigationGroup::make()
                    ->label('Hệ thống')
                    ->icon('heroicon-o-cog-6-tooth')
                    ->collapsed(false),
                \Filament\Navigation\NavigationGroup::make()
                    ->label('Báo cáo & Phân tích')
                    ->icon('heroicon-o-chart-bar')
                    ->collapsed(false),
                \Filament\Navigation\NavigationGroup::make()
                    ->label('Bảo mật')
                    ->icon('heroicon-o-shield-check')
                    ->collapsed(true),
                \Filament\Navigation\NavigationGroup::make()
                    ->label('Công cụ hệ thống')
                    ->icon('heroicon-o-wrench-screwdriver')
                    ->collapsed(true),
                \Filament\Navigation\NavigationGroup::make()
                    ->label('Chăm sóc bệnh nhân')
                    ->icon('heroicon-o-heart')
                    ->collapsed(true),
            ]);
    }
}
