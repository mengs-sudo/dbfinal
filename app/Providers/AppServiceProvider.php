<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Pagination\Paginator;

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
        Schema::defaultStringLength(125);

        // The app ships Bootstrap-5-flavored pagination CSS (.pagination,
        // .page-item, .page-link in resources/css/app.css), but Laravel's
        // out-of-the-box pagination view is Tailwind-based. Since this app
        // never loads Tailwind, the previous/next arrows were plain
        // un-styled inline SVGs rendering at their native (oversized) size
        // instead of picking up the small 24x24 .page-link box. Forcing the
        // Bootstrap-5 view here fixes the arrows everywhere paginate() is
        // used, including the Inventory and Inventory Valuation pages.
        Paginator::useBootstrapFive();
    }
}