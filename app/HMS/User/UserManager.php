<?php

namespace HMS\User;

use HMS\Entities\Role;
use HMS\Entities\User;
use HMS\Auth\PasswordStore;
use HMS\Repositories\UserRepository;
use HMS\User\Permissions\RoleManager;
use App\Events\Users\UserEmailChanged;
use Illuminate\Contracts\Auth\MustVerifyEmail;

class UserManager
{
    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * @var RoleManager
     */
    private $roleManager;

    /**
     * @var PasswordStore
     */
    private $passwordStore;

    /**
     * UserManager constructor.
     *
     * @param UserRepository $userRepository
     * @param RoleManager $roleManager
     * @param PasswordStore $passwordStore
     */
    public function __construct(
        UserRepository $userRepository,
        RoleManager $roleManager,
        PasswordStore $passwordStore
    ) {
        $this->userRepository = $userRepository;
        $this->roleManager = $roleManager;
        $this->passwordStore = $passwordStore;
    }

    /**
     * @param User $user
     * @param Role $role
     */
    public function removeRoleFromUser($user, $role)
    {
        $this->roleManager->removeUserFromRole($user, $role);
    }

    /**
     * @param string $firstname
     * @param string $lastname
     * @param string $username
     * @param string $email
     * @param string $password
     *
     * @return User
     */
    public function create(string $firstname, string $lastname, string $username, string $email, string $password)
    {
        $user = new User($firstname, $lastname, $username, $email);

        // TODO: maybe consolidate these into a single call via a service?
        $this->userRepository->save($user);
        $this->passwordStore->add($user->getUsername(), $password);

        $this->roleManager->addUserToRoleByName($user, Role::MEMBER_APPROVAL);

        return $user;
    }

    /**
     * Update the user form a form request.
     *
     * @param User $user User to update
     * @param array $request
     *
     * @return User
     */
    public function updateFromRequest(User $user, array $request): User
    {
        if (isset($request['firstname'])) {
            $user->setFirstname($request['firstname']);
        }

        if (isset($request['lastname'])) {
            $user->setLastname($request['lastname']);
        }

        if (isset($request['email']) && $request['email'] != $user->getEmail()) {
            $oldEmail = $user->getEmail();
            $user->setEmail($request['email']);
            if ($user instanceof MustVerifyEmail) {
                $user->setEmailVerifiedAt(null);
                $user->sendEmailVerificationNotification();
            }
            event(new UserEmailChanged($user, $oldEmail));
        }

        $this->userRepository->save($user);

        return $user;
    }
}
