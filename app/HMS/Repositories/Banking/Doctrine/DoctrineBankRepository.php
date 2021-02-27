<?php

namespace HMS\Repositories\Banking\Doctrine;

use HMS\Entities\Banking\Bank;
use Doctrine\ORM\EntityRepository;
use HMS\Repositories\Banking\BankRepository;
use LaravelDoctrine\ORM\Pagination\PaginatesFromRequest;

class DoctrineBankRepository extends EntityRepository implements BankRepository
{
    use PaginatesFromRequest;

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
     * @param string $sortCode
     * @param string $accountNumber
     *
     * @return null|Bank
     */
    public function findOneBySortCodeAndAccountNumber(string $sortCode, string $accountNumber)
    {
        return parent::findOneBy([
            'sortCode' => $sortCode,
            'accountNumber' => $accountNumber,
        ]);
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
