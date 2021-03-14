<?php

namespace App\Jobs\Banking;

use App\Notifications\Banking\UnmatchedTransaction;
use Carbon\Carbon;
use HMS\Entities\Banking\Bank;
use HMS\Entities\Role;
use HMS\Factories\Banking\BankTransactionFactory;
use HMS\Repositories\Banking\BankRepository;
use HMS\Repositories\RoleRepository;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use OfxParser\Parser as OfxParser;

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
     * @param RoleRepository $roleRepository
     * @param OfxParser $ofxParser
     *
     * @return void
     */
    public function handle(
        BankRepository $bankRepository,
        BankTransactionFactory $bankTransactionFactory,
        RoleRepository $roleRepository,
        OfxParser $ofxParser
    ) {
        $bank = $bankRepository->findOneById($this->bank_id);

        $ofx = $ofxParser->loadFromString($this->ofxString);

        $bankAccount = reset($ofx->bankAccounts);

        // TODO: should check that the $bank->getAccountNumber() is contained in $bankAccount->accountNumber

        // Get the statement transactions for the account
        $transactions = $bankAccount->statement->transactions;

        $unmatchedTransaction = [];

        foreach ($transactions as $transaction) {
            if (intval($transaction->uniqueId) < 200000000000000) {
                continue;
            }

            $bankTransaction = $bankTransactionFactory
                ->matchOrCreate(
                    $bank,
                    new Carbon($transaction->date),
                    $transaction->name . ' ' . $transaction->uniqueId,
                    intval($transaction->amount * 100)
                );

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
