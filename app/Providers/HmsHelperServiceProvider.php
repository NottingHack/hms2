<?php

namespace App\Providers;

use HMS\Helpers\SiteVisitor;
use HMS\Helpers\LabelPrinter;
use Illuminate\Support\Facades\App;
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
        App::bind('SiteVisitor', function () {
            return new SiteVisitor;
        });

        App::bind('LabelPrinter', function () {
            return new LabelPrinter;
        });
    }
}
