<?php

namespace HMS\Repositories\Gatekeeper;

use Carbon\Carbon;
use HMS\Entities\User;
use HMS\Entities\Gatekeeper\AccessLog;

interface AccessLogRepository
{
    /**
     * Finds all entities in the repository.
     *
     * @return array The entities.
     */
    public function findAll();

    /**
     * @return AccessLog|null
     */
    public function findFirst();

    /**
     * @param User $user
     *
     * @return AccessLog[]
     */
    public function findByUser(User $user);

    /**
     * @param User $user
     *
     * @return null|AccessLog
     */
    public function findLatestByUser(User $user);

    /**
     * @param int $perPage
     * @param string $pageName
     *
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function paginateAll($perPage = 15, $pageName = 'page');

    /**
     * @param User $user
     * @param int $perPage
     * @param string $pageName
     *
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function paginateByUser(User $user, $perPage = 15, $pageName = 'page');

    /**
     * Finds all entities in the repository between dates.
     *
     * @param Carbon $start
     * @param Carbon $end
     * @param int $perPage
     * @param string $pageName
     *
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function paginateBetweeenAccessTimes(Carbon $start, Carbon $end, $perPage = 15, $pageName = 'page');

    /**
     * Save AccessLog to the DB.
     *
     * @param AccessLog $accessLog
     */
    public function save(AccessLog $accessLog);
}
