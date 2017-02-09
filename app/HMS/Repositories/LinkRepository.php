<?php

namespace HMS\Repositories;

use HMS\Entities\Link;

interface LinkRepository
{
    /**
     * save Link to the DB.
     * @param  Link $Link
     */
    public function save(Link $link);

    /**
     * remove a Link from the DB.
     * @param  Link $line
     */
    public function remove(Link $link);

    /**
     * @param int    $perPage
     * @param string $pageName
     *
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function paginateAll($perPage = 15, $pageName = 'page');
}
