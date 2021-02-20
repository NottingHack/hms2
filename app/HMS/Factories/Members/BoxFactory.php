<?php

namespace HMS\Factories\Members;

use HMS\Entities\User;
use HMS\Entities\Members\Box;
use Illuminate\Support\Facades\Auth;
use HMS\Repositories\Members\BoxRepository;

class BoxFactory
{
    /**
     * @var BoxRepository
     */
    protected $boxRepository;

    /**
     * @param BoxRepository $boxRepository
     */
    public function __construct(BoxRepository $boxRepository)
    {
        $this->boxRepository = $boxRepository;
    }

    /**
     * Function to instantiate a new Box from given params.
     */
    public function create(?User $user = null)
    {
        $_box = new Box();
        $_box->setStateInUse();
        if ($user) {
            $_box->setUser($user);
        } else {
            $_box->setUser(Auth::user());
        }

        return $_box;
    }
}
