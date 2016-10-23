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
    /** @var IdentityManager  */
    protected $identityManager;

    public function __construct(Hasher $hasher, EntityManagerInterface $em, $entity, IdentityManager $identityManager)
    {
        // Note: $hasher is never used but required to construct DoctrineUserProvider (parent)
        parent::__construct($hasher, $em, $entity);

        $this->identityManager = $identityManager;
    }

    // overridden because getAuthIdentifier() on our User returns username rather than id
    public function retrieveById($identifier)
    {
        return $this->getRepository()->findOneBy(['username' => $identifier]);
    }

    // overridden because we don't store the password on the user, we use an IdentityManager to check it instead
    public function validateCredentials(IlluminateAuthenticatable $user, array $credentials)
    {
        return $this->identityManager->checkPassword($user->getAuthIdentifier(), $credentials['password']);
    }
}