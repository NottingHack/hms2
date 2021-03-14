<?php

namespace HMS\Repositories\Banking\Doctrine;

use Carbon\Carbon;
use Doctrine\ORM\EntityRepository;
use HMS\Entities\Banking\Account;
use HMS\Entities\Banking\Bank;
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
     * Find One By TransactionDate And Description And Amount.
     *
     * @param Bank $bank
     * @param Carbon $transactionDate
     * @param string $description
     * @param int $amount
     *
     * @return null|BankTransaction
     */
    public function findOneByBankAndDateAndDescriptionAndAmount(
        Bank $bank,
        Carbon $date,
        string $description,
        int $amount
    ) {
        return parent::findOneBy([
            'bank' => $bank,
            'transactionDate' => $date,
            'description' => $description,
            'amount' => $amount,
        ]);
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
     * Find the latest transaction for given Bank.
     *
     * @param Bank $bank
     *
     * @return null|BankTransaction
     */
    public function findLatestTransactionByBank(Bank $bank)
    {
        return parent::findOneByBank($bank, ['transactionDate' => 'DESC']);
    }

    /**
     * Find all unmatched transactions and paginate them.
     * Ordered by transactionDate DESC.
     *
     * @param int $perPage
     * @param string $pageName
     *
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function paginateUnmatched($perPage = 15, $pageName = 'page')
    {
        $qb = parent::createQueryBuilder('bankTransaction');

        $qb->where('bankTransaction.account IS NULL')
            ->andWhere('bankTransaction.transaction IS NULL')
            ->orderBy('bankTransaction.transactionDate', 'DESC');

        $q = $qb->getQuery();

        return $this->paginate($q, $perPage, $pageName);
    }

    /**
     * Find all transactions for a given Account and paginate them.
     * Ordered by transactionDate DESC.
     *
     * @param Account $account
     * @param int $perPage
     * @param string $pageName
     *
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function paginateByAccount(Account $account, $perPage = 15, $pageName = 'page')
    {
        $qb = parent::createQueryBuilder('bankTransaction');

        $qb->where('bankTransaction.account = :account_id')
            ->orderBy('bankTransaction.transactionDate', 'DESC');

        $qb->setParameter('account_id', $account->getId());

        $q = $qb->getQuery();

        return $this->paginate($q, $perPage, $pageName);
    }

    /**
     * Find all transactions for a given Bank and paginate them.
     * Ordered by transactionDate DESC.
     *
     * @param Bank $bank
     * @param int $perPage
     * @param string $pageName
     *
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function paginateByBank(Bank $bank, $perPage = 15, $pageName = 'page')
    {
        $qb = parent::createQueryBuilder('bankTransaction');

        $qb->where('bankTransaction.bank = :bank_id')
            ->orderBy('bankTransaction.transactionDate', 'DESC');

        $qb->setParameter('bank_id', $bank->getId());

        $q = $qb->getQuery();

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
        $bt = $this->findOneByBankAndDateAndDescriptionAndAmount(
            $bankTransaction->getBank(),
            $bankTransaction->getTransactionDate(),
            $bankTransaction->getDescription(),
            $bankTransaction->getAmount()
        );

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
