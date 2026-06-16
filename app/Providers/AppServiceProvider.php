<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Event;
use App\Models\Category;
use App\Services\Security\SecuritySettingsService;
use App\Services\Security\SecurityEventLogger;
use App\Services\Security\SecurityScannerService;
use App\Services\Security\SecurityFindingGuidanceService;
use App\Listeners\TrackLoginAttempt;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Failed;
use Illuminate\Auth\Events\Logout;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(SecuritySettingsService::class);
        $this->app->singleton(SecurityEventLogger::class);
        $this->app->singleton(SecurityScannerService::class);
        $this->app->singleton(SecurityFindingGuidanceService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if ($this->app->environment('production')) {
            \Illuminate\Support\Facades\URL::forceScheme('https');
        }

        $basePath = rtrim(request()->getBasePath(), '/');

        \Livewire\Livewire::setScriptRoute(function ($handle) use ($basePath) {
            return \Illuminate\Support\Facades\Route::get($basePath . '/livewire/livewire.js', $handle);
        });

        \Livewire\Livewire::setUpdateRoute(function ($handle) use ($basePath) {
            return \Illuminate\Support\Facades\Route::post($basePath . '/livewire/update', $handle)
                ->middleware('web');
        });

        View::composer('components.header', function ($view) {
            $mainCategories = \Illuminate\Support\Facades\Cache::remember('public_navigation_categories', now()->addHours(6), function () {
                return Category::where('parent_id', -1)
                    ->where('name', '!=', 'Chưa được phân loại')
                    ->with('children.children')
                    ->orderBy('order')
                    ->get();
            });
            $view->with('mainCategories', $mainCategories);
        });

        // Security: track login attempts via Laravel auth events
        Event::listen(Login::class,  [TrackLoginAttempt::class, 'handleLogin']);
        Event::listen(Failed::class, [TrackLoginAttempt::class, 'handleFailed']);
        Event::listen(Logout::class, [TrackLoginAttempt::class, 'handleLogout']);
    }
}
