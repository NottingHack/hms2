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
     * Find all transactions.
     *
     * @return array
     */
    public function findAll()
    {
        return parent::findAll();
    }

    /**
     * Find the latest transaction for each account.
     *
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
     * Find the latest transaction for given account.
     *
     * @param Account $account
     *
     * @return null|BankTransaction
     */
    public function findLatestTransactionByAccount(Account $account)
    {
        return parent::findOneByAccount($account, ['transactionDate' => 'DESC']);
    }

    /**
     * Find all transactions for a given account and pagineate them.
     * Ordered by transactionDate DESC.
     *
     * @param null|Account $account
     * @param int $perPage
     * @param string $pageName
     *
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function paginateByAccount(?Account $account, $perPage = 15, $pageName = 'page')
    {
        $q = parent::createQueryBuilder('bankTransaction');
        if (is_null($account)) {
            $q = $q->where('bankTransaction.account IS NULL')
                ->andWhere('bankTransaction.transaction IS NULL');
        } else {
            $q = $q->where('bankTransaction.account = :account_id');
            $q = $q->setParameter('account_id', $account->getId());
        }
        $q = $q->orderBy('bankTransaction.transactionDate', 'DESC');

        $q = $q->getQuery();

        return $this->paginate($q, $perPage, $pageName);
    }

    /**
     * Find a matching transaction in the db or save this one.
     *
     * @param BankTransaction $bankTransaction
     *
     * @return BankTransaction
     */
    public function findOrSave(BankTransaction $bankTransaction)
    {
        $bt = parent::findOneBy([
            'transactionDate' => $bankTransaction->getTransactionDate(),
            'description' => $bankTransaction->getDescription(),
            'amount' => $bankTransaction->getAmount(),
        ]);

        if ($bt) {
            return $bt;
        }

        $this->save($bankTransaction);

        return $bankTransaction;
    }

    /**
     * Save BankTransaction to the DB.
     *
     * @param BankTransaction $bankTransaction
     */
    public function save(BankTransaction $bankTransaction)
    {
        $this->_em->persist($bankTransaction);
        $this->_em->flush();
    }
}
