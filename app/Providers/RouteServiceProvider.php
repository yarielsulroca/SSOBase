<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to your application's "home" route.
     *
     * Typically, users are redirected here after authentication.
     *
     * @var string
     */
    public const HOME = '/home';

    /**
     * Define your route model bindings, pattern filters, and other route configuration.
     */
    public function boot(): void
    {
        // Rate limiting general para API
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });

        // Rate limiting específico para login
        RateLimiter::for('login', function (Request $request) {
            return Limit::perMinute(5)->by($request->input('username') . '|' . $request->ip());
        });

        // Registrar rutas API
        Route::prefix('api')
            ->middleware('api')
            ->group(base_path('routes/api.php'));

        // Registrar rutas Web
        Route::middleware('web')
            ->group(base_path('routes/web.php'));
    }
}
