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

        // Share total customer dan last update ke semua view
        View::composer('layouts.app', function ($view) {
            $view->with('totalCustomer', Cache::remember('total_customer_count', 3600, function() {
                return Customer::count();
            }));

            $view->with('lastUpdate', Cache::remember('last_sync_update_time', 3600, function() {
                $maxUpdated = Customer::max('updated_at');
                return $maxUpdated ? \Carbon\Carbon::parse($maxUpdated)->format('d/m H:i') : '-';
            }));
        });
    }
}