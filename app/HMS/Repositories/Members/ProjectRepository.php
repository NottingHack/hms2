<?php

namespace HMS\Repositories\Members;

use HMS\Entities\User;
use HMS\Entities\Members\Project;

interface ProjectRepository
{
    /**
     * Find by user.
     *
     * @param User $user
     *
     * @return Project[]
     */
    public function findByUser(User $user);

    /**
     * @param User $user
     * @param int $perPage
     * @param string $pageName
     * @param bool $active
     *
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function paginateByUser(User $user, $perPage = 15, $pageName = 'page', $active = false);

    /**
     * Return number of active projects for a user.
     *
     * @param User $user
     *
     * @return int
     */
    public function countActiveByUser(User $user);

    /**
     * Save Project to the DB.
     *
     * @param Project $project
     */
    public function save(Project $project);
}
