<?php

namespace App\Providers;

use App\Support\Theme;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

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
        View::composer('*', fn ($view) => $view->with('theme', Theme::current()));

        // Public site partials link to "#section" on the index page and to the inner pages elsewhere.
        View::composer('site.*', fn ($view) => $view->with('onHome', request()->is('/')));
    }
}
