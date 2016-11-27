<?php

namespace HMS\Accounts;

use HMS\Auth\PasswordStore;
use HMS\Entities\Role;
use HMS\Entities\User;
use HMS\Repositories\RoleRepository;
use HMS\Repositories\UserRepository;

class AccountManager
{
    /**
     * @var UserRepository
     */
    private $userRepository;
    /**
     * @var RoleRepository
     */
    private $roleRepository;
    /**
     * @var PasswordStore
     */
    private $passwordStore;

    /**
     * AccountManager constructor.
     * @param UserRepository $userRepository
     * @param RoleRepository $roleRepository
     * @param PasswordStore $passwordStore
     */
    public function __construct(UserRepository $userRepository,
        RoleRepository $roleRepository, PasswordStore $passwordStore)
    {
        $this->userRepository = $userRepository;
        $this->roleRepository = $roleRepository;
        $this->passwordStore = $passwordStore;
    }

    /**
     * @param string $name
     * @param string $username
     * @param string $email
     * @param string $password
     * @return User
     */
    public function create(string $name, string $username, string $email, string $password)
    {
        $user = new User($name, $username, $email);

        $user->getRoles()->add($this->roleRepository->findByName(Role::MEMBER_CURRENT));

        // TODO: maybe consolidate these into a single call via a service?
        $this->userRepository->create($user);
        $this->passwordStore->add($user->getUsername(), $password);

        return $user;
    }
}
