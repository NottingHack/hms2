<?php

namespace App\Jobs\Banking;

use App\Notifications\Banking\UnmatchedTransaction;
use Carbon\Carbon;
use HMS\Entities\Role;
use HMS\Factories\Banking\BankTransactionFactory;
use HMS\Repositories\Banking\BankRepository;
use HMS\Repositories\RoleRepository;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SaveNewTransactionsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var array
     */
    protected $transactions;

    /**
     * Create a new job instance.
     *
     * @param array $transactions
     *
     * @return void
     */
    public function __construct(array $transactions)
    {
        $this->transactions = $transactions;
    }

    /**
     * Execute the job.
     *
     * @param BankRepository $bankRepository
     * @param BankTransactionFactory $bankTransactionFactory
     * @param RoleRepository $roleRepository
     *
     * @return void
     */
    public function handle(
        BankRepository $bankRepository,
        BankTransactionFactory $bankTransactionFactory,
        RoleRepository $roleRepository
    ) {
        /**
         * Each transaction should be in the following form
         * {
         *     "sortCode" : "77-22-24",
         *     "accountNumber" : "13007568",
         *     "date" : "2017-07-17",
         *     "description" : "Edward Murphy HSNTSBBPRK86CWPV 4",
         *     "amount" : 500
         * }.
         */
        $unmatchedBank = [];
        $unmatchedTransaction = [];

        foreach ($this->transactions as $transaction) {
            $bank = $bankRepository->findOneBySortCodeAndAccountNumber(
                $transaction['sortCode'],
                $transaction['accountNumber']
            );

            if (is_null($bank)) {
                $unmatchedBank[] = $transaction;
            }

            $transactionDate = new Carbon($transaction['date']);

            $bankTransaction = $bankTransactionFactory
                ->matchOrCreate(
                    $bank,
                    $transactionDate,
                    $transaction['description'],
                    $transaction['amount']
                );

            // if this transaction has no account add it to our unmatched list
            if (is_null($bankTransaction->getAccount()) && is_null($bankTransaction->getTransaction())) {
                $unmatchedTransaction[] = $bankTransaction;
            }
        }

        // email finance team
        if (count($unmatchedBank) || count($unmatchedTransaction)) {
            $financeRole = $roleRepository->findOneByName(Role::TEAM_FINANCE);

            $financeRole->notify(new UnmatchedTransaction($unmatchedTransaction, $unmatchedBank));
        }
    }
}
