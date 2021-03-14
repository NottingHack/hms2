<?php

namespace HMS\Repositories\Banking\Doctrine;

use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityRepository;
use HMS\Entities\Banking\Bank;
use HMS\Entities\Banking\BankType;
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
     * Find all Bank that are not of type Automatic.
     *
     * @return Bank[]
     */
    public function findNotAutomatic()
    {
        $expr = Criteria::expr();
        $criteria = Criteria::create()
            ->where($expr->neq('type', BankType::AUTOMATIC));

        return $this->matching($criteria)->toArray();
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
