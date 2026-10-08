<?php

namespace App\Providers;

use App\Models\Parametro;
use Illuminate\Support\ServiceProvider;
use App\Observers\ParametroObserver;

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
        Parametro::observe(ParametroObserver::class);
    }
}
