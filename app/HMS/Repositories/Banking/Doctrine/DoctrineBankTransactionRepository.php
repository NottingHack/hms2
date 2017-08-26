<?php

namespace HMS\Repositories\Banking\Doctrine;

use HMS\Entities\Banking\Account;
use Doctrine\ORM\EntityRepository;
use HMS\Entities\Banking\BankTransaction;
use HMS\Repositories\Banking\BankTransactionRepository;

class DoctrineBankTransactionRepository extends EntityRepository implements BankTransactionRepository
{
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
     * find a matching tranaction in the db or save this one.
     * @param  BankTransaction $bankTransaction
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
     * save BankTransaction to the DB.
     * @param  BankTransaction $bankTransaction
     */
    public function save(BankTransaction $bankTransaction)
    {
        $this->_em->persist($bankTransaction);
        $this->_em->flush();
    }
}
