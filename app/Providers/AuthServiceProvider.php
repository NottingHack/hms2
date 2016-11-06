<?php

namespace App\Providers;

use Doctrine\ORM\EntityManagerInterface;
use HMS\Auth\HmsUserProvider;
use HMS\Auth\PasswordStore;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Auth;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        'App\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot(EntityManagerInterface $em, PasswordStore $passwordStore)
    {
        $this->registerPolicies();

        Auth::provider('hms', function($app, array $config) use ($em, $passwordStore) {

            return new HmsUserProvider($app['hash'], $em, $config['model'], $passwordStore);
        });
    }
}
