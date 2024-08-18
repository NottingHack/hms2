<?php

namespace HMS\Repositories\Governance;

use HMS\Entities\Governance\RegisterOfDirectors;
use HMS\Entities\User;

interface RegisterOfDirectorsRepository
{
    /**
     * @return RegisterOfDirectors[]
     */
    public function findAll();

    /**
     * Find the Current register entry for the given User
     *
     * @param User $user
     *
     * @return null|RegisterOfDirectors
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
     * Save RegisterOfDirectors to the DB.
     *
     * @param RegisterOfDirectors $registerOfDirectors
     */
    public function save(RegisterOfDirectors $registerOfDirectors);
}
