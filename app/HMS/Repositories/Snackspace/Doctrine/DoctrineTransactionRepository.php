<?php

namespace HMS\Repositories\Snackspace\Doctrine;

use App\Jobs\Snackspace\ProcessTransaction;
use Carbon\Carbon;
use Doctrine\ORM\EntityRepository;
use HMS\Entities\Snackspace\Transaction;
use HMS\Entities\Snackspace\TransactionState;
use HMS\Entities\User;
use HMS\Repositories\Snackspace\TransactionRepository;
use LaravelDoctrine\ORM\Pagination\PaginatesFromRequest;

class DoctrineTransactionRepository extends EntityRepository implements TransactionRepository
{
    use PaginatesFromRequest;

    /**
     * Generate Payment report between given dates.
     *
     * @param Carbon $startDate
     * @param Carbon $endDate
     *
     * @return \Illuminate\Support\Collection
     */
    public function reportPaymnetsBetween(Carbon $startDate, Carbon $endDate)
    {
        $q = parent::createQueryBuilder('transactions');

        $q->select('transactions.type')
            ->addSelect('SUM(transactions.amount) as amount')
            ->where('transactions.status = :status')
            ->andWhere($q->expr()->between('transactions.transactionDatetime', ':start', ':end'))
            ->groupBy('transactions.type');

        $q = $q->setParameter('status', TransactionState::COMPLETE)
            ->setParameter('start', $startDate->copy())
            ->setParameter('end', $endDate->copy())
            ->getQuery();

        $results = collect($q->getResult());
        $results = $results->mapWithKeys(function ($row) {
            return [
                $row['type'] => $row['amount'],
            ];
        });

        return $results;
    }

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

        ProcessTransaction::dispatch($transaction);

        return $transaction;
    }
}
