<?php

namespace HMS\Repositories\Banking\Doctrine;

use HMS\Entities\Banking\Account;
use Doctrine\ORM\EntityRepository;
use HMS\Repositories\Banking\AccountRepository;

class DoctrineAccountRepository extends EntityRepository implements AccountRepository
{
    /**
     * @param  $id
     * @return null|Account
     */
    public function findOneById($id)
    {
        return parent::findOneById($id);
    }

    /**
     * @return Account[]
     */
    public function findAll()
    {
        return parent::findAll();
    }

    /**
     * @param  string $paymentRef
     * @return null|Account
     */
    public function findOneByPaymentRef(string $paymentRef)
    {
        return parent::findOneByPaymentRef($paymentRef);
    }

    /**
     * @param  string $paymentRef
     * @return Account[]
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
