<?php

namespace App\Providers;

use HMS\Helpers\HmsHelper;
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
        App::bind('HmsHelper', function()
        {
            return new HmsHelper;
        });
    }
}
