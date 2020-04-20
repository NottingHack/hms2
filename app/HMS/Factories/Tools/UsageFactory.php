<?php

namespace HMS\Factories\Tools;

use Carbon\Carbon;
use HMS\Entities\User;
use HMS\Entities\Tools\Tool;
use HMS\Entities\Tools\Usage;
use HMS\Entities\Tools\UsageState;
use HMS\Repositories\Tools\UsageRepository;

class UsageFactory
{
    /**
     * @var UsageRepository
     */
    protected $usageRepository;

    /**
     * @param UsageRepository $usageRepository
     */
    public function __construct(UsageRepository $usageRepository)
    {
        $this->usageRepository = $usageRepository;
    }

    /**
     * Function to instantiate a new Usage from given params.
     *
     * @return Usage
     */
    public function createFreeTime(Tool $tool, User $user, int $duration)
    {
        $_usage = new Usage();

        $_usage->setTool($tool);
        $_usage->setUser($user);
        $_usage->setDuration($duration);
        $_usage->setStatus(UsageState::COMPLETE);
        $_usage->setStart(Carbon::now());
        $_usage->setActiveTime(0);

        return $_usage;
    }
}
