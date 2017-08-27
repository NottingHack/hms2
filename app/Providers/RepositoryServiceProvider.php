<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use HMS\Repositories\PermissionRepository;
use LaravelDoctrine\ACL\Permissions\Permission;
use HMS\Repositories\Doctrine\DoctrinePermissionRepository;

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
        foreach (config('repositories.repositories') as $repository) {
            $entity = config('repositories.entity_namespace') . '\\' . $repository;
            $interface = config('repositories.repository_namespace') . '\\' . $repository . 'Repository';
            $implmentation = config('repositories.repository_namespace') . '\\' .
                (strpos($repository, '\\') ? explode('\\', $repository)[0] . '\\' : '') .
                'Doctrine\\Doctrine' .
                (strpos($repository, '\\') ? explode('\\', $repository)[1] : $repository) .
                'Repository';

            $this->app->singleton($interface, function ($app) use ($implmentation, $entity) {
                return new $implmentation($app['em'], $app['em']->getClassMetaData($entity));
            });
        }

        // Special case so do this one by hand
        $this->app->singleton(PermissionRepository::class, function ($app) {
            return new DoctrinePermissionRepository($app['em'], $app['em']->getClassMetaData(Permission::class));
        });
    }
}
