<?php

namespace HMS\Auth;

use Doctrine\ORM\EntityManagerInterface;
use Illuminate\Contracts\Hashing\Hasher;
use LaravelDoctrine\ORM\Auth\DoctrineUserProvider;
use Illuminate\Contracts\Auth\Authenticatable as IlluminateAuthenticatable;

/**
 * @author Rob Hunt <rob.hunt@nottinghack.org.uk>
 */
class HmsUserProvider extends DoctrineUserProvider
{
    /**
     * @var PasswordStore
     */
    protected $passwordStore;

    /**
     * Construct new HmsUserProvider.
     *
     * @param Hasher $hasher
     * @param EntityManagerInterface $em
     * @param string $entity
     * @param PasswordStore $passwordStore
     */
    public function __construct(
        Hasher $hasher,
        EntityManagerInterface $em,
        string $entity,
        PasswordStore $passwordStore
    ) {
        // Note: $hasher is never used but required to construct DoctrineUserProvider (parent)
        parent::__construct($hasher, $em, $entity);

        $this->passwordStore = $passwordStore;
    }

    // overridden because we don't store the password on the user, we use an PasswordStore to check it instead
    public function validateCredentials(IlluminateAuthenticatable $user, array $credentials)
    {
        return $this->passwordStore->checkPassword($user->getUsername(), $credentials['password']);
    }
}
