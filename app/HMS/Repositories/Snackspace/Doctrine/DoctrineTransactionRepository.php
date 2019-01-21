<?php

namespace HMS\Repositories\Snackspace\Doctrine;

use HMS\Entities\User;
use Doctrine\ORM\EntityRepository;
use HMS\Entities\Snackspace\Transaction;
use HMS\Entities\Snackspace\TransactionState;
use HMS\Repositories\Snackspace\TransactionRepository;
use LaravelDoctrine\ORM\Pagination\PaginatesFromRequest;

class DoctrineTransactionRepository extends EntityRepository implements TransactionRepository
{
    use PaginatesFromRequest;

    /**
     * Find all transactions for a given user and pagineate them.
     * Ordered by transactionDatetime DESC.
     *
     * @param User $user
     * @param int $perPage
     * @param string $pageName
     *
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function paginateByUser(User $user, $perPage = 15, $pageName = 'page')
    {
        $q = parent::createQueryBuilder('transactions')
            ->where('transactions.user = :user_id')
            ->orderBy('transactions.transactionDatetime', 'DESC');

        $q = $q->setParameter('user_id', $user->getId())->getQuery();

        return $this->paginate($q, $perPage, $pageName);
    }

    /**
     * Save Transaction to the DB and update the users balance.
     *
     * @param Transaction $transaction
     *
     * @return Transaction
     */
    public function saveAndUpdateBalance(Transaction $transaction)
    {
        $transaction->getUser()->getProfile()->updateBalanceByAmount($transaction->getAmount());
        $transaction->setStatus(TransactionState::COMPLETE);
        $this->_em->persist($transaction);
        $this->_em->flush();

        return $transaction;
    }

    /**
     * Save Transaction to the DB.
     *
     * @param Transaction $transaction
     */
    public function save(Transaction $transaction)
    {
        $this->_em->persist($transaction);
        $this->_em->flush();
    }
}
