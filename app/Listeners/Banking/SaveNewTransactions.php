<?php

namespace App\Listeners\Banking;

use Carbon\Carbon;
use HMS\Entities\Role;
use HMS\Repositories\RoleRepository;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Events\Banking\TransactionsUploaded;
use HMS\Repositories\Banking\BankRepository;
use HMS\Factories\Banking\BankTransactionFactory;
use App\Notifications\Banking\UnmatchedTransaction;
use HMS\Repositories\Banking\BankTransactionRepository;

class SaveNewTransactions implements ShouldQueue
{
    /**
     * @var BankRepository
     */
    protected $bankRepository;

    /**
     * @var BankTransactionFactory
     */
    protected $bankTransactionFactory;

    /**
     * @var BankTransactionRepository
     */
    protected $bankTransactionRepository;

    /**
     * @var RoleRepository
     */
    protected $roleRepository;

    /**
     * Create the event listener.
     *
     * @param BankRepository $bankRepository
     * @param BankTransactionFactory $bankTransactionFactory
     * @param BankTransactionRepository $bankTransactionRepository
     * @param RoleRepository $roleRepository
     */
    public function __construct(BankRepository $bankRepository,
        BankTransactionFactory $bankTransactionFactory,
        BankTransactionRepository $bankTransactionRepository,
        RoleRepository $roleRepository)
    {
        $this->bankRepository = $bankRepository;
        $this->bankTransactionFactory = $bankTransactionFactory;
        $this->bankTransactionRepository = $bankTransactionRepository;
        $this->roleRepository = $roleRepository;
    }

    /**
     * Handle the event.
     *
     * @param  TransactionsUploaded  $event
     * @return void
     */
    public function handle(TransactionsUploaded $event)
    {
        /**
         * each transaction should be in the following form
         * {
         *     "sortCode" : "77-22-24",
         *     "accountNumber" : "13007568",
         *     "date" : "2017-07-17",
         *     "description" : "Edward Murphy HSNTSBBPRK86CWPV 4",
         *     "amount" : 5.00
         * }.
         */
        $unmatchedBank = [];
        $unmatchedTransaction = [];

        foreach ($event->transactions as $transaction) {
            $bank = $this->bankRepository->findOneByAccountNumber($transaction['accountNumber']);
            if (is_null($bank)) {
                $unmatchedBank[] = $transaction;
            }

            $transactionDate = new Carbon($transaction['date']);

            $bankTransaction = $this->bankTransactionFactory->create($bank, $transactionDate, $transaction['description'], $transaction['amount']);

            // now see if we already have this transaction on record? before saving it
            $bankTransaction = $this->bankTransactionRepository->findOrSave($bankTransaction);

            // if this transaction has no account add it to our unmatched list
            if (is_null($bankTransaction->getAccount())) {
                $unmatchedTransaction[] = $bankTransaction;
            }
        }

        // email finance team
        if (count($unmatchedBank) || count($unmatchedTransaction)) {
            $financeRole = $this->roleRepository->findOneByName(Role::TEAM_FINANCE);

            $financeRole->notify(new UnmatchedTransaction($unmatchedTransaction, $unmatchedBank));
        }
    }
}
