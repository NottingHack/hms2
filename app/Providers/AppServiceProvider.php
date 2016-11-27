<?php

namespace App\Providers;

use HMS\Auth\PasswordStore;
use HMS\Auth\PasswordStoreManager;
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
        $this->app->singleton(PasswordStore::class, function($app) {
            $passwordStoreManager = new PasswordStoreManager($app);

            return $passwordStoreManager->driver();
        });
    }
}
