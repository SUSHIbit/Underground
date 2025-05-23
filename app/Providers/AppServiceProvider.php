<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Carbon\Carbon;

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
        // Set the default timezone for all Carbon instances to Malaysia
        Carbon::setLocale('en');
        date_default_timezone_set('Asia/Kuala_Lumpur');
        Carbon::setToStringFormat('Y-m-d H:i:s');
    }
}