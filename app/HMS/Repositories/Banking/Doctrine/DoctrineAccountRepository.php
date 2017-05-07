<?php

namespace HMS\Repositories\Banking\Doctrine;

use HMS\Entities\Banking\Account;
use Doctrine\ORM\EntityRepository;
use HMS\Repositories\Banking\AccountRepository;

class DoctrineAccountRepository extends EntityRepository implements AccountRepository
{
    /**
     * @param  $id
     * @return array
     */
    public function find($id)
    {
        return parent::find($id);
    }

    /**
     * @param  string $paymentRef
     * @return array
     */
    public function findOneByPaymentRef(string $paymentRef)
    {
        return parent::findOneByPaymentRef($paymentRef);
    }

    /**
     * @param  string $paymentRef
     * @return array
     */
    public function findLikeByPaymentRef(string $paymentRef)
    {
        $q = parent::createQueryBuilder('a')
          ->where('a.paymentRef LIKE :paymentRef')
          ->setParameter('paymentRef', '%'.$paymentRef.'%')
          ->getQuery();

        return $q->getResult();
    }

    /**
     * save Account to the DB.
     * @param  Account $account
     */
    public function save(Account $account)
    {
        $this->_em->persist($account);
        $this->_em->flush();
    }
}
