<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use Illuminate\Support\Facades\View;
use App\Models\Category;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $basePath = rtrim(request()->getBasePath(), '/');

        \Livewire\Livewire::setScriptRoute(function ($handle) use ($basePath) {
            return \Illuminate\Support\Facades\Route::get($basePath . '/livewire/livewire.js', $handle);
        });

        \Livewire\Livewire::setUpdateRoute(function ($handle) use ($basePath) {
            return \Illuminate\Support\Facades\Route::post($basePath . '/livewire/update', $handle);
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
    }
}
