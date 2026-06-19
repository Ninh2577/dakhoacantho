<?php

namespace App\Providers;

use App\Listeners\TrackLoginAttempt;
use App\Models\Category;
use App\Models\User;
use App\Services\Security\SecurityEventLogger;
use App\Services\Security\SecurityFindingGuidanceService;
use App\Services\Security\SecurityScannerService;
use App\Services\Security\SecuritySettingsService;
use Illuminate\Auth\Events\Failed;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;

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
        Gate::define('access-admin-api', function (User $user) {
            return $user->role === 'admin';
        });

        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }

        $basePath = rtrim(request()->getBasePath(), '/');

        Livewire::setScriptRoute(function ($handle) use ($basePath) {
            return Route::get($basePath.'/livewire/livewire.js', $handle);
        });

        Livewire::setUpdateRoute(function ($handle) use ($basePath) {
            return Route::post($basePath.'/livewire/update', $handle)
                ->middleware('web');
        });

        View::composer('components.header', function ($view) {
            $mainCategories = Cache::remember('public_navigation_categories', now()->addHours(6), function () {
                return Category::where('parent_id', -1)
                    ->where('name', '!=', 'Chưa được phân loại')
                    ->with('children.children')
                    ->orderBy('order')
                    ->get();
            });
            $view->with('mainCategories', $mainCategories);
        });

        // Media Cleanup: Automatically delete physical files when records are deleted
        \App\Models\Article::deleting(function ($article) {
            $images = [
                $article->featured_image,
                $article->thumbnail_image,
                $article->og_image,
                $article->twitter_image,
            ];
            foreach (array_unique(array_filter($images)) as $image) {
                if (\Illuminate\Support\Facades\Storage::disk('public')->exists($image)) {
                    \Illuminate\Support\Facades\Storage::disk('public')->delete($image);
                }
            }
        });

        \App\Models\Category::deleting(function ($category) {
            if ($category->featured_image && \Illuminate\Support\Facades\Storage::disk('public')->exists($category->featured_image)) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($category->featured_image);
            }
        });

        // Security: track login attempts via Laravel auth events
        Event::listen(Login::class, [TrackLoginAttempt::class, 'handleLogin']);
        Event::listen(Failed::class, [TrackLoginAttempt::class, 'handleFailed']);
        Event::listen(Logout::class, [TrackLoginAttempt::class, 'handleLogout']);
    }
}
