<?php

namespace HMS\Repositories\Governance;

use HMS\Entities\Governance\RegisterOfMembers;
use HMS\Entities\User;

interface RegisterOfMembersRepository
{
    /**
     * @return RegisterOfMembers[]
     */
    public function findAll();

    /**
     * Find the Current register entry for the given User.
     *
     * @param User $user
     *
     * @return null|RegisterOfMembers
     */
    public function findCurrentByUser(User $user);

    /**
     * @param int $perPage
     * @param string $pageName
     *
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function paginateAll($perPage = 15, $pageName = 'page');

    /**
     * Save RegisterOfMembers to the DB.
     *
     * @param RegisterOfMembers $registerOfMembers
     */
    public function save(RegisterOfMembers $registerOfMembers);
}
