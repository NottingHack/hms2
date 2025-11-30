<?php

namespace HMS\Repositories\Banking\Doctrine;

use Doctrine\ORM\EntityRepository;
use HMS\Entities\Banking\Account;
use HMS\Repositories\Banking\AccountRepository;
use LaravelDoctrine\ORM\Pagination\PaginatesFromRequest;

class DoctrineAccountRepository extends EntityRepository implements AccountRepository
{
    use PaginatesFromRequest;

    /**
     * @param int $id
     *
     * @return null|Account
     */
    public function findOneById(int $id)
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
     * @param string $paymentRef
     *
     * @return null|Account
     */
    public function findOneByPaymentRef(string $paymentRef)
    {
        return parent::findOneByPaymentRef($paymentRef);
    }

    /**
     * @param string $legacyRef
     *
     * @return null|Account
     */
    public function findOneByLegacyRef(string $legacyRef)
    {
        return parent::findOneByLegacyRef($legacyRef);
    }

    /**
     * @param string $paymentRef
     *
     * @return Account[]
     */
    public function findLikeByPaymentRef(string $paymentRef)
    {
        $q = parent::createQueryBuilder('a')
          ->where('a.paymentRef LIKE :paymentRef')
          ->setParameter('paymentRef', '%' . $paymentRef . '%')
          ->getQuery();

        return $q->getResult();
    }

    /**
     * @param int $perPage
     * @param string $pageName
     *
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function paginateJointAccounts($perPage = 15, $pageName = 'page')
    {
        $q = parent::createQueryBuilder('a')
            ->leftJoin('a.users', 'user')
            ->where('user.account IS NOT NULL')
            ->groupBy('a.id, a.paymentRef')
            ->having('COUNT(0) > 1');

        $q = $q->getQuery();

        return $this->paginate($q, $perPage, $pageName);
    }

    /**
     * Save Account to the DB.
     *
     * @param Account $account
     */
    public function save(Account $account)
    {
        $this->_em->persist($account);
        $this->_em->flush();
    }
}
