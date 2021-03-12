<?php

namespace App\Providers;

use HMS\Helpers\Features;
use HMS\Helpers\SiteVisitor;
use Illuminate\Support\ServiceProvider;

class HmsHelperServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('Features', function () {
            return new Features;
        });

        $this->app->bind('SiteVisitor', function () {
            return new SiteVisitor;
        });
    }
}
