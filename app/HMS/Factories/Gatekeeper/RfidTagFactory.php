<?php

namespace HMS\Factories\Gatekeeper;

use Carbon\Carbon;
use HMS\Entities\Gatekeeper\RfidTag;
use HMS\Entities\Gatekeeper\RfidTagState;
use HMS\Entities\User;
use HMS\Repositories\Gatekeeper\RfidTagRepository;

class RfidTagFactory
{
    /**
     * @var RfidTagRepository
     */
    protected $rfidTagRepository;

    /**
     * @param RfidTagRepository $rfidTagRepository
     */
    public function __construct(RfidTagRepository $rfidTagRepository)
    {
        $this->rfidTagRepository = $rfidTagRepository;
    }

    /**
     * Function to instantiate a new RfidTag from given params.
     *
     * @param User $user
     * @param string $rfidSerial
     * @param string $state
     *
     * @return RfidTag
     */
    public function create(
        User $user,
        string $rfidSerial,
        string $state = RfidTagState::ACTIVE
    ) {
        $_rfidTag = new RfidTag();

        $_rfidTag->setUser($user);
        $_rfidTag->setRfidSerial($rfidSerial);
        $_rfidTag->setState($state);
        $_rfidTag->setLastUsed(Carbon::now());

        return $_rfidTag;
    }
}
