<?php

namespace HMS\Repositories\Members;

use HMS\Entities\User;
use HMS\Entities\Members\Box;

interface BoxRepository
{
    /**
     * Count all boxes.
     *
     * @return int Total number of boxes INUSE
     */
    public function count(array $criteria = []);

    /**
     * Count all boxes INUSE.
     *
     * @return int Total number of boxes INUSE
     */
    public function countAllInUse();

    /**
     * Count all boxes REMOVED.
     *
     * @return int Total number of boxes REMOVED
     */
    public function countAllRemoved();

    /**
     * Count all boxes ABANDONED.
     *
     * @return int Total number of boxes ABANDONED
     */
    public function countAllAbandoned();

    /**
     * Count how many boxes a User has INUSE.
     *
     * @param User $user
     *
     * @return int Number of boxes this user has INUSE
     */
    public function countInUseByUser(User $user);

    /**
     * @param User $user
     * @param int $perPage
     * @param string $pageName
     *
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function paginateByUser(User $user, $perPage = 15, $pageName = 'page');

    /**
     * Save Box to the DB.
     *
     * @param Box $box
     */
    public function save(Box $box);
}
