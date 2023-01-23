<?php

namespace App\Providers;

use App\HMS\Auth\HmsPassportUserRepository;
use Doctrine\ORM\EntityManagerInterface;
use HMS\Auth\HmsUserProvider;
use HMS\Auth\PasswordStore;
use HMS\Entities\User;
use Laravel\Passport\Bridge\RefreshTokenRepository;
use Laravel\Passport\Passport;
use Laravel\Passport\PassportServiceProvider as LaravelPassportServiceProvider;
use League\OAuth2\Server\Grant\PasswordGrant;

class PassportServiceProvider extends LaravelPassportServiceProvider
{
    /**
     * Create and configure a Password grant instance.
     *
     * @return PasswordGrant
     */
    protected function makePasswordGrant()
    {
        $provider = new HmsUserProvider(
            $this->app['hash'],
            $this->app[EntityManagerInterface::class],
            User::class,
            $this->app[PasswordStore::class]
        );

        $userRepository = new HmsPassportUserRepository($provider);

        $grant = new PasswordGrant(
            $userRepository,
            $this->app->make(RefreshTokenRepository::class)
        );

        $grant->setRefreshTokenTTL(Passport::refreshTokensExpireIn());

        return $grant;
    }
}
