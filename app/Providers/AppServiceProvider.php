<?php

namespace App\Providers;

use HMS\Auth\PasswordStore;
use Faker\Factory as FakerFactory;
use HMS\Auth\PasswordStoreManager;
use Faker\Generator as FakerGenerator;
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
        $this->app->singleton(PasswordStore::class, function ($app) {
            $passwordStoreManager = new PasswordStoreManager($app);

            return $passwordStoreManager->driver();
        });

        $this->app->singleton(FakerGenerator::class, function () {
            return FakerFactory::create('en_GB');
        });
    }
}
