<?php

namespace App\Providers;

use App\Models\User;
use App\Support\Theme;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\RateLimiter;
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
        RateLimiter::for('chat', fn (Request $request) => Limit::perMinute(10)->by($request->ip()));
        Gate::define('super-admin', fn (User $user): bool => $user->isSuperAdmin() && $user->is_active);
        RateLimiter::for('otp-verify', fn (Request $request) => Limit::perMinute(10)->by($request->ip()));
        View::composer('*', fn ($view) => $view->with('theme', Theme::current()));

        // Public site partials link to "#section" on the index page and to the inner pages elsewhere.
        View::composer('site.*', fn ($view) => $view->with('onHome', request()->is('/')));
    }
}
