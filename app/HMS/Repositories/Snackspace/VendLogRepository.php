<?php

namespace HMS\Repositories\Snackspace;

use HMS\Entities\Snackspace\VendLog;
use HMS\Entities\Snackspace\VendingMachine;

interface VendLogRepository
{
    /**
     * Save VendLog to the DB.
     *
     * @param VendLog $vendLog
     */
    public function save(VendLog $vendLog);

    /**
     * Save VendLog to the DB and update the users balance.
     *
     * @param VendLog $vendLog
     *
     * @return VendLog
     */
    public function saveAndUpdateBalance(VendLog $vendLog);

    /**
     * @param int $perPage
     * @param string $pageName
     *
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function paginateAll($perPage = 15, $pageName = 'page');

    /**
     * Paginate logs for a Machine.
     * Ordered by id DESC.
     *
     * @param VendingMachine $vendingMachine
     * @param int $perPage
     * @param string $pageName
     *
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function paginateByVendingMachine(VendingMachine $vendingMachine, $perPage = 15, $pageName = 'page');
}
