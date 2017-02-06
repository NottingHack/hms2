<?php

namespace HMS\Repositories;

use HMS\Entities\Link;

interface LinkRepository
{
    /**
     * save Link to the DB.
     * @param  User $user
     */
    public function save(Link $link);

    /**
     * @param int    $perPage
     * @param string $pageName
     *
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function paginateAll($perPage = 15, $pageName = 'page');
}
