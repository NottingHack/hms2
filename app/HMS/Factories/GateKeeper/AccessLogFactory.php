<?php

namespace HMS\Factories\GateKeeper;

use HMS\Entities\GateKeeper\AccessLog;
use HMS\Repositories\GateKeeper\AccessLogRepository;

class AccessLogFactory
{
    /**
     * @var AccessLogRepository
     */
    protected $accessLogRepository;

    /**
     * @param AccessLogRepository $accessLogRepository
     */
    public function __construct(AccessLogRepository $accessLogRepository)
    {
        $this->accessLogRepository = $accessLogRepository;
    }

    /**
     * Function to instantiate a new AccessLog from given params.
     */
    public function create()
    {
        $_accessLog = new AccessLog();

        return $_accessLog;
    }
}
