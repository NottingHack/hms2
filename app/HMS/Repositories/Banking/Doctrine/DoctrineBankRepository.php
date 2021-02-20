<?php

namespace HMS\Repositories\Banking\Doctrine;

use HMS\Entities\Banking\Bank;
use Doctrine\ORM\EntityRepository;
use HMS\Repositories\Banking\BankRepository;

class DoctrineBankRepository extends EntityRepository implements BankRepository
{
    /**
     * @param int $id
     *
     * @return null|Bank
     */
    public function findOneById(int $id)
    {
        return parent::findOneById($id);
    }

    /**
     * @param string $accountNumber
     *
     * @return null|Bank
     */
    public function findOneByAccountNumber(string $accountNumber)
    {
        return parent::findOneByAccountNumber($accountNumber);
    }

    /**
     * Save Bank to the DB.
     *
     * @param Bank $bank
     */
    public function save(Bank $bank)
    {
        $this->_em->persist($bank);
        $this->_em->flush();
    }
}
