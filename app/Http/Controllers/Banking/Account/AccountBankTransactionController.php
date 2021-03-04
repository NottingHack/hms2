<?php

namespace App\Http\Controllers\Banking\Account;

use Carbon\Carbon;
use HMS\Helpers\Features;
use Illuminate\Http\Request;
use HMS\Entities\Banking\Account;
use HMS\Entities\Banking\BankType;
use App\Http\Controllers\Controller;
use App\Jobs\Banking\AccountAuditJob;
use HMS\Repositories\Banking\BankRepository;
use HMS\Factories\Banking\BankTransactionFactory;

class AccountBankTransactionController extends Controller
{
    /**
     * @var BankTransactionFactory
     */
    protected $bankTransactionFactory;

    /**
     * @var BankRepository
     */
    protected $bankRepository;

    /**
     * @var Features
     */
    protected $features;

    /**
     * Create a new controller instance.
     *
     * @param BankTransactionFactory $bankTransactionFactory
     * @param BankRepository $bankRepository
     * @param Features $features
     */
    public function __construct(
        BankTransactionFactory $bankTransactionFactory,
        BankRepository $bankRepository,
        Features $features
    ) {
        $this->bankTransactionFactory = $bankTransactionFactory;
        $this->bankRepository = $bankRepository;
        $this->features = $features;

        $this->middleware('can:bankTransactions.edit');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param  Account  $account
     * @return \Illuminate\Http\Response
     */
    public function create(Account $account)
    {
        $banks = $this->bankRepository->findNotAutomatic();

        // TODO: if all $banks are Type CASH and $this->features->isDisabled('cash_membership_payments') BAIL

        if (count($banks) == 0) {
            flash('All defined Banks are type Automatic. Transactions can not be enter manually.')
                ->error();

            return back();
        }

        return view('banking.accounts.transactions.create')
            ->with('account', $account)
            ->with('banks', $banks);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  Account  $account
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Account $account)
    {
        $validatedData = $request->validate([
            'bank_id' => 'required|exists:HMS\Entities\Banking\Bank,id',
            'transactionDate' => 'required|date',
            'description' => 'required|string|max:512',
            'amount' => 'required|integer',
        ]);

        $bank = $this->bankRepository->findOneById($validatedData['bank_id']);

        if (BankType::AUTOMATIC == $bank->getType()) {
            flash('Bank ' . $bank->getName() . ' is type Automatic. Transactions can not be enter manually.')
                ->error();

            return redirect()->route('banking.accounts.show', $account->getId());
        } elseif (BankType::CASH == $bank->getType()
             && $this->features->isEnabled('cash_membership_payments')) {
            flash('Bank ' . $bank->getName() . ' is type Cash and cash membership payments are not currently allowed.')
                ->error();

            return redirect()->route('banking.accounts.show', $account->getId());
        }

        $transactionDate = new Carbon($validatedData['transactionDate']);

        $bankTransaction = $this->bankTransactionFactory
            ->matchOrCreate(
                $bank,
                $transactionDate,
                $validatedData['description'],
                $validatedData['amount'],
                $account
            );

        AccountAuditJob::dispatch($account);

        flash('Bank Transaction \'' . $bankTransaction->getDescription() . '\' created.')->success();

        return redirect()->route('banking.accounts.show', $account->getId());
    }
}
