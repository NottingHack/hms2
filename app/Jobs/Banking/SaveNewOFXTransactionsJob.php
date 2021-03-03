<?php

namespace App\Jobs\Banking;

use Carbon\Carbon;
use HMS\Entities\Role;
use Illuminate\Bus\Queueable;
use HMS\Entities\Banking\Bank;
use OfxParser\Parser as OfxParser;
use HMS\Repositories\RoleRepository;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use HMS\Repositories\Banking\BankRepository;
use HMS\Factories\Banking\BankTransactionFactory;
use App\Notifications\Banking\UnmatchedTransaction;
use HMS\Repositories\Banking\BankTransactionRepository;

class SaveNewOFXTransactionsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var int
     */
    protected $bank_id;

    /**
     * @var string
     */
    protected $ofxString;

    /**
     * Create a new job instance.
     *
     * @param string $ofxString
     *
     * @return void
     */
    public function __construct(Bank $bank, string $ofxString)
    {
        $this->bank_id = $bank->getId();
        $this->ofxString = $ofxString;
    }

    /**
     * Execute the job.
     *
     * @param BankRepository $bankRepository
     * @param BankTransactionFactory $bankTransactionFactory
     * @param BankTransactionRepository $bankTransactionRepository
     * @param RoleRepository $roleRepository
     * @param OfxParser $ofxParser
     *
     * @return void
     */
    public function handle(
        BankRepository $bankRepository,
        BankTransactionFactory $bankTransactionFactory,
        BankTransactionRepository $bankTransactionRepository,
        RoleRepository $roleRepository,
        OfxParser $ofxParser
    ) {
        $bank = $bankRepository->findOneById($this->bank_id);

        $ofx = $ofxParser->loadFromString($this->ofxString);

        $bankAccount = reset($ofx->bankAccounts);
        // Get the statement transactions for the account
        $transactions = $bankAccount->statement->transactions;

        $unmatchedTransaction = [];

        foreach ($transactions as $transaction) {
            if (intval($transaction->uniqueId) < 200000000000000) {
                continue;
            }

            $bankTransaction = $bankTransactionFactory
                ->create(
                    $bank,
                    new Carbon($transaction->date),
                    $transaction->name,
                    intval($transaction->amount * 100)
                );

            // now see if we already have this transaction on record? before saving it
            $bankTransaction = $bankTransactionRepository->findOrSave($bankTransaction);

            // if this transaction has no account add it to our unmatched list
            if (is_null($bankTransaction->getAccount()) && is_null($bankTransaction->getTransaction())) {
                $unmatchedTransaction[] = $bankTransaction;
            }
        }

        // email finance team
        if (count($unmatchedTransaction)) {
            $financeRole = $roleRepository->findOneByName(Role::TEAM_FINANCE);

            $financeRole->notify(new UnmatchedTransaction($unmatchedTransaction, []));
        }
    }
}
