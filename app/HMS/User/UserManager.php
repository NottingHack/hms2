<?php

namespace HMS\User;

use HMS\Entities\Role;
use HMS\Entities\User;
use HMS\Auth\PasswordStore;
use HMS\Repositories\RoleRepository;
use HMS\Repositories\UserRepository;

class UserManager
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
     * UserManager constructor.
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

    public function removeRoleFromUser($userId, $role)
    {
        $user = $this->userRepository->find($userId);


        $user->getRoles()->removeElement($role);

        // TODO: this should be ->save()
        $this->userRepository->create($user);
    }

    /**
     * @param string $firstname
     * @param string $lastname
     * @param string $username
     * @param string $email
     * @param string $password
     * @return User
     */
    public function create(string $firstname, string $lastname, string $username, string $email, string $password)
    {
        $user = new User($firstname, $lastname, $username, $email);

        $user->getRoles()->add($this->roleRepository->findByName(Role::MEMBER_CURRENT));

        // TODO: maybe consolidate these into a single call via a service?
        $this->userRepository->create($user);
        $this->passwordStore->add($user->getUsername(), $password);

        return $user;
    }

    /**
     * Find a user and format it to send to a view.
     *
     * @param int $id The id of the user we want
     * @return array
     */
    public function getFormattedUser($id)
    {
        $user = $this->userRepository->find($id);

        return $this->formatUser($user);
    }

    /**
     * Format the user to send to the view.
     *
     * @param \HMS\Entities\User $user The user to format
     * @return array
     */
    private function formatUser($user)
    {
        $formattedUser = [
                'id'            =>  $user->getId(),
                'name'          =>  $user->getName(),
                ];

        return $formattedUser;
    }
}
