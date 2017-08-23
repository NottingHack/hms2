<?php

namespace HMS\Repositories\Banking\Doctrine;

use HMS\Entities\Banking\Account;
use Doctrine\ORM\EntityRepository;
use HMS\Entities\Banking\BankTransaction;
use HMS\Repositories\Banking\BankTransactionRepository;
use LaravelDoctrine\ORM\Pagination\PaginatesFromRequest;

class DoctrineBankTransactionRepository extends EntityRepository implements BankTransactionRepository
{
    use PaginatesFromRequest;

    /**
     * find the latest transaction for each account.
     * @return array
     */
    public function findLatestTransactionForAllAccounts()
    {
        $q = parent::createQueryBuilder('bankTransaction')
            ->addSelect('MAX(bankTransaction.transactionDate) AS latestTransactionDate')
            ->groupBy('bankTransaction.account')
            ->where('bankTransaction.account IS NOT NULL');

        return $q->getQuery()->getResult();
    }

    /**
     * find the latest transaction for given account.
     * @param  Account $account
     * @return null|BankTransaction
     */
    public function findLatestTransactionByAccount(Account $account)
    {
        return parent::findOneByAccount($account, ['transactionDate' => 'DESC']);
    }

    /**
     * find all transactions for a fiven account and pagineate them.
     * Ordered by transactionDate DESC.
     *
     * @param Account   $account
     * @param int    $perPage
     * @param string $pageName
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function paginateByAccount(Account $account, $perPage = 15, $pageName = 'page')
    {
        $q = parent::createQueryBuilder('bankTransaction')
            ->where('bankTransaction.account = :account_id')
            ->orderBy('bankTransaction.transactionDate', 'DESC');

        $q = $q->setParameter('account_id', $account->getId())->getQuery();

        return $this->paginate($q, $perPage, $pageName);
    }

    /**
     * save BankTransaction to the DB.
     * @param  BankTransaction $bankTransaction
     */
    public function save(BankTransaction $bankTransaction)
    {
        $this->_em->persist($bankTransaction);
        $this->_em->flush();
    }
}
