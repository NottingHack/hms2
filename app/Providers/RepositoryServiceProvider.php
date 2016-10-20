<?php

namespace App\Providers;

use HMS\Repositories\DoctrineUserRepository;
use HMS\Repositories\UserRepository;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
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
        $this->app->singleton(UserRepository::class, function($app) {
            return $app->make(DoctrineUserRepository::class);
        });
    }
}
