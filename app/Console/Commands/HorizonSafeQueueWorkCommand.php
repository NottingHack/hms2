<?php

namespace App\Console\Commands;

use MaxBrokman\SafeQueue\Worker;
use Laravel\Horizon\Console\WorkCommand;
use Illuminate\Contracts\Cache\Repository as Cache;

class HorizonSafeQueueWorkCommand extends WorkCommand
{
    /**
     * @var string
     */
    protected $description = 'Start processing jobs on the queue as a daemon in a doctrine-safe way.';

    /**
     * WorkCommand constructor.
     *
     * @param Worker $worker
     * @param Cache  $cache
     */
    public function __construct(Worker $worker, Cache $cache)
    {
        parent::__construct($worker, $cache);
    }
}
