<?php

namespace HMS\Factories\Governance;

use HMS\Entities\User;
use HMS\Entities\Governance\Proxy;
use HMS\Entities\Governance\Meeting;
use HMS\Repositories\Governance\ProxyRepository;

class ProxyFactory
{
    /**
     * @var ProxyRepository
     */
    protected $proxyRepository;

    /**
     * @param ProxyRepository $proxyRepository
     */
    public function __construct(ProxyRepository $proxyRepository)
    {
        $this->proxyRepository = $proxyRepository;
    }

    /**
     * Function to instantiate a new Proxy from given params.
     *
     * @param Meeting $meeting
     * @param User $proxy
     * @param User $principal
     *
     * @return Proxy
     */
    public function create(Meeting $meeting, User $proxy, User $principal)
    {
        $_proxy = new Proxy();

        $_proxy->setMeeting($meeting);
        $_proxy->setProxy($proxy);
        $_proxy->setPrincipal($principal);

        return $_proxy;
    }
}
