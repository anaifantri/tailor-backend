<?php

namespace App\Providers;

use App\Models\OrderDetail;
use App\Observers\OrderDetailObserver;
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
        OrderDetail::observe(OrderDetailObserver::class);
    }
}
