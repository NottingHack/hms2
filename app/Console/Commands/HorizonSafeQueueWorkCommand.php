<?php

namespace App\Console\Commands;

use MaxBrokman\SafeQueue\Worker;
use Laravel\Horizon\Console\WorkCommand;

class HorizonSafeQueueWorkCommand extends WorkCommand
{
    /**
     * @var string
     */
    protected $description = 'Start processing jobs on the queue as a daemon in a doctrine-safe way.';

    public function __construct(Worker $worker)
    {
        parent::__construct($worker);
    }
}
