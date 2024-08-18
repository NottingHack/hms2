<?php

namespace HMS\Factories\Governance;

use Carbon\Carbon;
use HMS\Entities\Governance\RegisterOfMembers;
use HMS\Entities\User;
use HMS\Repositories\Governance\RegisterOfMembersRepository;

class RegisterOfMembersFactory
{
    /**
     * @var RegisterOfMembersRepository
     */
    protected $registerOfMembersRepository;

    /**
     * @param RegisterOfMembersRepository $registerOfMembersRepository
     */
    public function __construct(RegisterOfMembersRepository $registerOfMembersRepository)
    {
        $this->registerOfMembersRepository = $registerOfMembersRepository;
    }

    /**
     * Function to instantiate a new RegisterOfMembers from given params.
     *
     * @param User $user
     * @param string $firstname
     * @param string $lastname
     * @param Carbon $startedAt
     * @param null|Carbon $endedAt
     *
     * @return RegisterOfMembers
     */
    public function create(
        User $user,
        string $firstname,
        string $lastname,
        Carbon $startedAt,
        Carbon $endedAt = null
    ) {
        $_registerOfMembers = new RegisterOfMembers();

        $_registerOfMembers->setUser($user);
        $_registerOfMembers->setFirstname($firstname);
        $_registerOfMembers->setLastname($lastname);
        $_registerOfMembers->setStartedAt($startedAt);

        if ($endedAt) {
            $_registerOfMembers->setEndedAt($endedAt);
        }

        return $_registerOfMembers;
    }
}
