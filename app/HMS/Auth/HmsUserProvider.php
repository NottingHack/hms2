<?php

namespace HMS\Auth;

use Doctrine\ORM\EntityManagerInterface;
use Illuminate\Contracts\Auth\Authenticatable as IlluminateAuthenticatable;
use Illuminate\Contracts\Hashing\Hasher;
use LaravelDoctrine\ORM\Auth\DoctrineUserProvider;

/**
 * @author Rob Hunt <rob.hunt@nottinghack.org.uk>
 */
class HmsUserProvider extends DoctrineUserProvider
{
    /** @var PasswordStore  */
    protected $passwordStore;

    public function __construct(Hasher $hasher, EntityManagerInterface $em, $entity, PasswordStore $passwordStore)
    {
        // Note: $hasher is never used but required to construct DoctrineUserProvider (parent)
        parent::__construct($hasher, $em, $entity);

        $this->passwordStore = $passwordStore;
    }

    // overridden because getAuthIdentifier() on our User returns username rather than id
    public function retrieveById($identifier)
    {
        return $this->getRepository()->findOneBy(['username' => $identifier]);
    }

    // overridden because we don't store the password on the user, we use an PasswordStore to check it instead
    public function validateCredentials(IlluminateAuthenticatable $user, array $credentials)
    {
        return $this->passwordStore->checkPassword($user->getAuthIdentifier(), $credentials['password']);
    }
}