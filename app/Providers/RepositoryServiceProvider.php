<?php

namespace App\Providers;

use HMS\Repositories\Doctrine\DoctrinePermissionRepository;
use HMS\Repositories\PermissionRepository;
use Illuminate\Support\ServiceProvider;
use LaravelDoctrine\ACL\Permissions\Permission;

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

            preg_match('/(.*?)(\\\\)*([^\\\\]+)$/', $repository, $matches);

            if ($matches[2]) {
                $implementation = config('repositories.repository_namespace') . '\\' .
                $matches[1] .
                '\\Doctrine\\Doctrine' .
                $matches[3] .
                'Repository';
            } else {
                $implementation = config('repositories.repository_namespace') . '\\' .
                'Doctrine\\Doctrine' .
                $matches[3] .
                'Repository';
            }

            $this->app->singleton($interface, function ($app) use ($implementation, $entity) {
                return new $implementation($app['em'], $app['em']->getClassMetaData($entity));
            });
        }

        // Special case so do this one by hand
        $this->app->singleton(PermissionRepository::class, function ($app) {
            return new DoctrinePermissionRepository($app['em'], $app['em']->getClassMetaData(Permission::class));
        });
    }
}
