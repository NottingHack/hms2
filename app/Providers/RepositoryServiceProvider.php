<?php

namespace App\Providers;

use HMS\Entities\Meta;
use HMS\Entities\Role;
use HMS\Entities\User;
use HMS\Entities\Invite;
use HMS\Repositories\MetaRepository;
use HMS\Repositories\RoleRepository;
use HMS\Repositories\UserRepository;
use HMS\Repositories\InviteRepository;
use Illuminate\Support\ServiceProvider;
use HMS\Repositories\DoctrineUserRepository;

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
        $this->app->singleton(MetaRepository::class, function ($app) {
            return new MetaRepository($app['em'], $app['em']->getClassMetaData(Meta::class));
        });

        $this->app->singleton(InviteRepository::class, function ($app) {
            return new InviteRepository($app['em'], $app['em']->getClassMetaData(Invite::class));
        });

        $this->app->singleton(RoleRepository::class, function ($app) {
            return new RoleRepository($app['em'], $app['em']->getClassMetaData(Role::class));
        });

        $this->app->singleton(UserRepository::class, function ($app) {
            return new DoctrineUserRepository($app['em'], $app['em']->getClassMetaData(User::class));
        });
    }
}
