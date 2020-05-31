<?php

namespace HMS\Repositories\Gatekeeper;

use HMS\Entities\User;
use HMS\Entities\Gatekeeper\RfidTag;

interface RfidTagRepository
{
    /**
     * @return RfidTag[]
     */
    public function findAll();

    /**
     * @param User $user
     *
     * @return RfidTag[]
     */
    public function findByUser(User $user);

    /**
     * @param User $user
     * @param int $perPage
     * @param string $pageName
     *
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function paginateByUser(User $user, $perPage = 15, $pageName = 'page');

    /**
     * Find a tag via serial Number.
     *
     * @param string  $rfidSerial
     * @param bool $activeOnly Only search ACTIVE tags
     *
     * @return RfidTag|null
     */
    public function findByRfidSerial(string $rfidSerial, $activeOnly = false);

    /**
     * Save RfidTag to the DB.
     *
     * @param RfidTag $rfidTag
     */
    public function save(RfidTag $rfidTag);

    /**
     * Remove a RfidTag from the DB.
     *
     * @param RfidTag $rfidTag
     */
    public function remove(RfidTag $rfidTag);
}
