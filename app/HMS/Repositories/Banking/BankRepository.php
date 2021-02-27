<?php

namespace HMS\Repositories\Banking;

use HMS\Entities\Banking\Bank;

interface BankRepository
{
    /**
     * @param int $id
     *
     * @return null|Bank
     */
    public function findOneById(int $id);

    /**
     * @param string $sortCode
     * @param string $accountNumber
     *
     * @return null|Bank
     */
    public function findOneBySortCodeAndAccountNumber(string $sortCode, string $accountNumber);

    /**
     * Find all Bank that are not of type Automatic.
     *
     * @return Bank[]
     */
    public function findNotAutomatic();

    /**
     * Save Bank to the DB.
     *
     * @param Bank $bank
     */
    public function save(Bank $bank);

    /**
     * @param int $perPage
     * @param string $pageName
     *
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function paginateAll($perPage = 15, $pageName = 'page');
}
