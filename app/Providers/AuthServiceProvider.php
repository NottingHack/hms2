<?php

namespace App\Providers;

use Doctrine\ORM\EntityManagerInterface;
use HMS\Auth\HmsUserProvider;
use HMS\Auth\IdentityManager;
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
    public function boot(IdentityManager $identityManager)
    {
        $this->registerPolicies();

        Auth::provider('hms', function($app, array $config) use ($identityManager) {
            return new HmsUserProvider($app['hash'], $app->make(EntityManagerInterface::class), $config['model'], $identityManager);
        });
    }
}
