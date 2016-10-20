<?php

namespace App\Providers;

use HMS\Auth\FileBasedIdentityManager;
use HMS\Auth\IdentityManager;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
        $this->app->singleton(IdentityManager::class, function($app) {
            return $app->make(FileBasedIdentityManager::class);
        });
    }
}
