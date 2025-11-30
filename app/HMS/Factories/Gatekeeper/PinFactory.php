<?php

namespace HMS\Factories\Gatekeeper;

use HMS\Entities\Gatekeeper\Pin;
use HMS\Entities\Gatekeeper\PinState;
use HMS\Entities\User;
use HMS\Repositories\Gatekeeper\PinRepository;

class PinFactory
{
    /**
     * @var PinRepository
     */
    protected $pinRepository;

    /**
     * @param PinRepository $pinRepository
     */
    public function __construct(PinRepository $pinRepository)
    {
        $this->pinRepository = $pinRepository;
    }

    /**
     * Create a new Pin for rfid card enrollment.
     *
     * @param User $user
     *
     * @return Pin
     */
    public function createNewEnrollPinForUser(User $user)
    {
        $pin = new Pin($this->generateUniquePin(), PinState::ENROLL);
        $pin->setUser($user);

        return $pin;
    }

    /**
     * Generate a random pin.
     *
     * @return string A random pin.
     */
    protected function generatePin()
    {
        // Currently a PIN is a 4 digit number between 1000 and 9999
        return (string) rand(1000, 9999);
    }

    /**
     * Generate a unique (at the time this function was called) pin.
     *
     * @return string A random pin that was not in the database at the time this function was called.
     */
    protected function generateUniquePin()
    {
        // A loop hiting the database? Why not...
        do {
            $pin = $this->generatePin();
        } while ($this->pinRepository->findOneByPin($pin) !== null);

        return $pin;
    }
}
