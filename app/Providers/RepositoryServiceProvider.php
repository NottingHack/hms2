<?php

namespace App\Providers;

use HMS\Entities\Role;
use HMS\Entities\Invite;
use HMS\Repositories\RoleRepository;
use HMS\Repositories\DoctrineUserRepository;
use HMS\Repositories\UserRepository;
use HMS\Repositories\InviteRepository;
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
        $this->app->singleton(InviteRepository::class, function ($app) {
            return new InviteRepository($app['em'], $app['em']->getClassMetaData(Invite::class));
        });

        $this->app->singleton(RoleRepository::class, function ($app) {
            return new RoleRepository($app['em'], $app['em']->getClassMetaData(Role::class));
        });

        $this->app->singleton(UserRepository::class, function($app) {
            return $app->make(DoctrineUserRepository::class);
        });
    }
}
