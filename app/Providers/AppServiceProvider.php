<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Cache;
use App\Models\Customer;

use Illuminate\Pagination\Paginator;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Paginator::useBootstrapFive();

        // Paksa HTTPS di lingkungan produksi agar form action menggunakan https://
        if (config('app.env') === 'production') {
            \Illuminate\Support\Facades\URL::forceScheme('https');
        }

        // Share total customer ke semua view
        View::composer('layouts.app', function ($view) {
            $view->with('totalCustomer', Cache::remember('total_customer_count', 3600, function() {
                return Customer::count();
            }));
        });
    }
}