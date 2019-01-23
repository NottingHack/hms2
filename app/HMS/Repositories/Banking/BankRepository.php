<?php

namespace HMS\Repositories\Banking;

use HMS\Entities\Banking\Bank;

interface BankRepository
{
    /**
     * @param $id
     *
     * @return null|Bank
     */
    public function findOneById($id);

    /**
     * @param string $accountNumber
     *
     * @return null|Bank
     */
    public function findOneByAccountNumber(string $accountNumber);

    /**
     * Save Bank to the DB.
     *
     * @param Bank $bank
     */
    public function save(Bank $bank);
}
