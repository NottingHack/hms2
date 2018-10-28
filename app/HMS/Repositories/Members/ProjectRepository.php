<?php

namespace HMS\Repositories\Members;

use HMS\Entities\User;
use HMS\Entities\Members\Project;

interface ProjectRepository
{
    /**
     * @param User   $user
     * @param int    $perPage
     * @param string $pageName
     *
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function paginateByUser(User $user, $perPage = 15, $pageName = 'page');

    /**
     * Return number of active projects for a user.
     * @param  User   $user
     *
     * @return int
     */
    public function countActiveByUser(User $user);

    /**
     * save Project to the DB.
     * @param  Project $project
     */
    public function save(Project $project);
}
