<?php

namespace HMS\User;

use HMS\Repositories\UserRepository;

class UserManager
{

    private $userRepository;

    /**
     * Create a new RoleManager instance
     *
     * @param HMS\Repositories\UserRepository $userRepository An instance of a user repository
     */
    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * Find a user and format it to send to a view
     *
     * @param Integer $id The id of the user we want
     * @return Array
     */
    public function getFormattedUser($id)
    {
        $user = $this->userRepository->find($id);

        return $this->formatUser($user);
    }

    /**
     * Format the user to send to the view
     *
     * @param \HMS\Entities\User $user The user to format
     * @return Array
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
