<?php

namespace HMS\Repositories\Snackspace;

use Carbon\Carbon;
use HMS\Entities\Snackspace\Debt;

interface DebtRepository
{
    /**
     * Finds all entities in the repository.
     *
     * @return Debt[]
     */
    public function findAll();

    /**
     * Finds all entities in the repository between dates.
     *
     * @param Carbon $start
     * @param Carbon $end
     *
     * @return Debt[]
     */
    public function findBetweeenAuditTimes(Carbon $start, Carbon $end);

    /**
     * Find lastest entry.
     *
     * @return null|Debt
     */
    public function findLatest();

    /**
     * Save Debt to the DB.
     *
     * @param Debt $debt
     */
    public function save(Debt $debt);
}
