<?php

namespace App\HMS\Auth;

use Illuminate\Contracts\Auth\UserProvider;
use Laravel\Passport\Bridge\User as PassportUser;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Entities\UserEntityInterface;
use League\OAuth2\Server\Repositories\UserRepositoryInterface;

final class HmsPassportUserRepository implements UserRepositoryInterface
{
    /**
     * The user provider implementation.
     *
     * @var UserProvider
     */
    private $userProvider;

    /**
     * UserRepository constructor.
     *
     * @param UserProvider $userProvider
     */
    public function __construct(UserProvider $userProvider)
    {
        $this->userProvider = $userProvider;
    }

    /**
     * Get a user entity.
     *
     * @param string                $username
     * @param string                $password
     * @param string                $grantType The grant type used
     * @param ClientEntityInterface $clientEntity
     *
     * @return UserEntityInterface|null
     */
    public function getUserEntityByUserCredentials(
        $username,
        $password,
        $grantType,
        ClientEntityInterface $clientEntity
    ) {
        $field = filter_var($username, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        if (! $user = $this->userProvider->retrieveByCredentials([$field => $username])) {
            return;
        }

        if (! $this->userProvider->validateCredentials($user, ['password' => $password])) {
            return;
        }

        return new PassportUser($user->getAuthIdentifier());
    }
}
