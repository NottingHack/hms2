<?php

namespace App\Providers;

use HMS\Helpers\Features;
use HMS\Helpers\SiteVisitor;
use Illuminate\Support\ServiceProvider;

class HmsHelperServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot(): void
    {
        //
    }

    /**
     * Register the application services.
     */
    public function register(): void
    {
        $this->app->bind('Features', function () {
            return new Features;
        });

        $this->app->bind('SiteVisitor', function () {
            return new SiteVisitor;
        });
    }
}
