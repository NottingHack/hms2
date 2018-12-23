<?php

namespace HMS\Repositories\Members;

use HMS\Entities\User;
use HMS\Entities\Members\Box;

interface BoxRepository
{
    /**
     * Count all boxes INUSE.
     *
     * @return int Total number of boxes INUSE
     */
    public function countAllInUse();

    /**
     * Count how many boxes a User has INUSE.
     * @param User $user
     *
     * @return int Number of boxes this user has INUSE
     */
    public function countInUseByUser(User $user);

    /**
     * @param User   $user
     * @param int    $perPage
     * @param string $pageName
     *
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function paginateByUser(User $user, $perPage = 15, $pageName = 'page');

    /**
     * save Box to the DB.
     * @param  Box $box
     */
    public function save(Box $box);
}
