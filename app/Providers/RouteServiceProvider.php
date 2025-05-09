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
    public static function home()
    {
        if (auth()->check()) {
            $user = auth()->user();
            
            if ($user->role === 'lecturer') {
                return '/lecturer/dashboard';
            } elseif ($user->role === 'accessor') {
                return '/accessor/dashboard';
            } elseif ($user->role === 'admin') {
                return '/admin/dashboard';
            } elseif ($user->is_judge) {
                return '/judge/dashboard';  // Redirect users with is_judge to judge dashboard
            }
        }
        
        return '/dashboard';
    }

    /**
     * Define your route model bindings, pattern filters, and other route configuration.
     */
    public function boot(): void
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });

        $this->routes(function () {
            Route::middleware('api')
                ->prefix('api')
                ->group(base_path('routes/api.php'));

            Route::middleware('web')
                ->group(base_path('routes/web.php'));
        });
    }
}