<?php

namespace HMS\Repositories\Governance;

use HMS\Entities\User;
use HMS\Entities\Governance\Proxy;
use HMS\Entities\Governance\Meeting;

interface ProxyRepository
{
    /**
     * Find all Proxies for a meeting by Proxy (User).
     *
     * @param Meeting $meeting
     * @param User $proxy
     *
     * @return Proxy[]
     */
    public function findByProxy(Meeting $meeting, User $proxy);

    /**
     * Find Proxy for a meeting by Principal (User).
     *
     * @param Meeting $meeting
     * @param User $principal
     *
     * @return Proxy|null
     */
    public function findOneByPrincipal(Meeting $meeting, User $principal);

    /**
     * For a given meeting count the proxied represented by the Checked-in Attendees.
     *
     * @param Meeting $meeting
     *
     * @return int
     */
    public function countRepresentedForMeeting(Meeting $meeting);

    /**
     * Save Proxy to the DB.
     *
     * @param Proxy $proxy
     */
    public function save(Proxy $proxy);

    /**
     * Remove a single Proxy.
     *
     * @param Proxy $proxy
     */
    public function remove(Proxy $proxy);
}
