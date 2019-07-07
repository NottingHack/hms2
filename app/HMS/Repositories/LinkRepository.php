<?php

namespace HMS\Repositories;

use HMS\Entities\Link;

interface LinkRepository
{
    /**
     * Save Link to the DB.
     *
     * @param Link $link
     */
    public function save(Link $link);

    /**
     * Remove a Link from the DB.
     *
     * @param Link $link
     */
    public function remove(Link $link);

    /**
     * @param int $perPage
     * @param string $pageName
     *
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function paginateAll($perPage = 15, $pageName = 'page');
}
