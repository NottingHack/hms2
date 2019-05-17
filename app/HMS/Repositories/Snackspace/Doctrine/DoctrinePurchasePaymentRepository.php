<?php

namespace HMS\Repositories\Snackspace\Doctrine;

use HMS\Entities\User;
use Doctrine\ORM\EntityRepository;
use HMS\Entities\Snackspace\Transaction;
use HMS\Entities\Snackspace\PurchasePayment;
use HMS\Repositories\Snackspace\PurchasePaymentRepository;

class DoctrinePurchasePaymentRepository extends EntityRepository implements PurchasePaymentRepository
{
    /**
     * Sum how much of the current transaction has been allocated.
     *
     * @param Transaction $transaction
     *
     * @return int
     */
    public function sumAllocated(Transaction $transaction)
    {
        if ($transaction->getAmount() == 0) {
            return 0;
        }

        $q = parent::createQueryBuilder('pp');

        if ($transaction->getAmount() < 0) {
            // This is a purchase
            $q->select('-1*COALESCE(SUM(pp.amount),0)')
                ->where('pp.purchase = :transaction_id');
        } else {
            // This is a payment
            $q->select('COALESCE(SUM(pp.amount),0)')
                ->where('pp.payment = :transaction_id');
        }
        $q->setParameter('transaction_id', $transaction->getId());

        return (int) $q->getQuery()->getSingleScalarResult() ?? 0;
    }

    /**
     * Sum unallocated payments for User before Transaction.
     *
     * @param User $user
     * @param Transaction $transaction
     *
     * @return int
     */
    public function sumUnallocatedPayments(User $user, Transaction $transaction)
    {
        $subquery = parent::createQueryBuilder('pp')
            ->select('COALESCE(SUM(pp.amount),0)')
            ->where('pp.payment = t.id')
            ->andWhere('pp.purchase < :transaction_id')
            ->andWhere('pp.payment  < :transaction_id')
            ->getDQL();

        $q = $this->_em->createQueryBuilder();
        $q->select('COALESCE(SUM(t.amount),0)')
            ->from(Transaction::class, 't')
            ->where('t.user = :user_id')
            ->andWhere('t.id < :transaction_id')
            ->andWhere('t.amount > 0')
            ->andWhere($q->expr()->gt('t.amount', '(' . $subquery . ')'));

        $q->setParameter('transaction_id', $transaction->getId());
        $q->setParameter('user_id', $transaction->getUser()->getId());

        return (int) $q->getQuery()->getSingleScalarResult() ?? 0;
    }

    /**
     * Sum unallocated purchases for User before Transaction.
     *
     * @param User $user
     * @param Transaction $transaction
     *
     * @return int
     */
    public function sumUnallocatedPurchases(User $user, Transaction $transaction)
    {
        $subquery = parent::createQueryBuilder('pp')
            ->select('COALESCE(SUM(pp.amount),0)')
            ->where('pp.purchase = t.id')
            ->andWhere('pp.purchase < :transaction_id')
            ->andWhere('pp.payment  < :transaction_id')
            ->getDQL();

        $q = $this->_em->createQueryBuilder();
        $q->select('COALESCE(SUM(-1*t.amount),0)')
            ->from(Transaction::class, 't')
            ->where('t.user = :user_id')
            ->andWhere('t.id < :transaction_id')
            ->andWhere('t.amount < 0')
            ->andWhere($q->expr()->gt('-1*t.amount', '(' . $subquery . ')'));

        $q->setParameter('transaction_id', $transaction->getId());
        $q->setParameter('user_id', $transaction->getUser()->getId());

        return (int) $q->getQuery()->getSingleScalarResult() ?? 0;
    }

    /**
     * Find unallocated payments for User before Transaction.
     *
     * @param User $user
     * @param Transaction $transaction
     *
     * @return array
     */
    public function findUnallocatedPayments(User $user, Transaction $transaction)
    {
        $subquery1 = parent::createQueryBuilder('pp')
            ->select('COALESCE(SUM(pp.amount),0)')
            ->where('pp.payment = t.id')
            ->getDQL();

        $subquery2 = parent::createQueryBuilder('pp2')
            ->select('COALESCE(SUM(pp2.amount),0)')
            ->where('pp2.payment = t.id')
            ->andWhere('pp2.purchase <= :transaction_id')
            ->getDQL();

        $q = $this->_em->createQueryBuilder();
        $q->select('t as transaction')
            ->addSelect('(' . $subquery1 . ') AS amountAllocated')
            ->from(Transaction::class, 't')
            ->where('t.user = :user_id')
            ->andWhere('t.id < :transaction_id')
            ->andWhere('t.amount > 0')
            ->andWhere($q->expr()->gt('t.amount', '(' . $subquery2 . ')'))
            ->orderBy('t.id');

        $q->setParameter('transaction_id', $transaction->getId());
        $q->setParameter('user_id', $transaction->getUser()->getId());

        // Because I can not get docritne to CAST this
        $results = $q->getQuery()->getResult();
        array_walk($results, function (&$value) {
            $value['amountAllocated'] = (int) $value['amountAllocated'];
        });

        return $results;
    }

    /**
     * Find unallocated purchases for User before Transaction.
     *
     * @param User $user
     * @param Transaction $transaction
     *
     * @return array
     */
    public function findUnallocatedPurchases(User $user, Transaction $transaction)
    {
        $subquery1 = parent::createQueryBuilder('pp')
            ->select('COALESCE(SUM(pp.amount),0)')
            ->where('pp.purchase = t.id')
            ->getDQL();

        $subquery2 = parent::createQueryBuilder('pp2')
            ->select('COALESCE(SUM(pp2.amount),0)')
            ->where('pp2.purchase = t.id')
            ->andWhere('pp2.payment <= :transaction_id')
            ->getDQL();

        $q = $this->_em->createQueryBuilder();
        $q->select('t as transaction')
            ->addSelect('(' . $subquery1 . ') AS amountAllocated')
            ->from(Transaction::class, 't')
            ->where('t.user = :user_id')
            ->andWhere('t.id < :transaction_id')
            ->andWhere('t.amount < 0')
            ->andWhere($q->expr()->gt('-1*t.amount', '(' . $subquery2 . ')'))
            ->orderBy('t.id');

        $q->setParameter('transaction_id', $transaction->getId());
        $q->setParameter('user_id', $transaction->getUser()->getId());

        // Because I can not get docritne to CAST this
        $results = $q->getQuery()->getResult();
        array_walk($results, function (&$value) {
            $value['amountAllocated'] = (int) $value['amountAllocated'];
        });

        return $results;
    }

    /**
     * Save PurchasePayment to the DB.
     *
     * @param PurchasePayment $purchasePayment
     */
    public function save(PurchasePayment $purchasePayment)
    {
        $this->_em->persist($purchasePayment);
        $this->_em->flush();
    }
}
