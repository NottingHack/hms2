<?php

namespace App\Http\Controllers\Banking\Account;

use Carbon\Carbon;
use Illuminate\Http\Request;
use HMS\Entities\Banking\Account;
use HMS\Entities\Banking\BankType;
use App\Http\Controllers\Controller;
use HMS\Repositories\MetaRepository;
use App\Jobs\Banking\AccountAuditJob;
use HMS\Repositories\Banking\BankRepository;
use HMS\Factories\Banking\BankTransactionFactory;
use HMS\Repositories\Banking\BankTransactionRepository;

class AccountBankTransactionController extends Controller
{
    /**
     * @var BankTransactionRepository
     */
    protected $bankTransactionRepository;

    /**
     * @var BankTransactionFactory
     */
    protected $bankTransactionFactory;

    /**
     * @var BankRepository
     */
    protected $bankRepository;

    /**
     * @var MetaRepository
     */
    protected $metaRepository;

    /**
     * Create a new controller instance.
     *
     * @param BankTransactionRepository $bankTransactionRepository
     * @param BankTransactionFactory $bankTransactionFactory
     * @param BankRepository $bankRepository
     * @param MetaRepository $metaRepository
     */
    public function __construct(
        BankTransactionRepository $bankTransactionRepository,
        BankTransactionFactory $bankTransactionFactory,
        BankRepository $bankRepository,
        MetaRepository $metaRepository
    ) {
        $this->bankTransactionRepository = $bankTransactionRepository;
        $this->bankTransactionFactory = $bankTransactionFactory;
        $this->bankRepository = $bankRepository;
        $this->metaRepository = $metaRepository;

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

        // TODO: if all $banks are Type CASH and Meta::getInt('allow_cash_membership_payments', 0) is false BAIL

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
             && $this->metaRepository->getInt('allow_cash_membership_payments', 0) == false) {
            flash('Bank ' . $bank->getName() . ' is type Cash and cash membership payments are not currently allowed.')
                ->error();

            return redirect()->route('banking.accounts.show', $account->getId());
        }

        $transactionDate = new Carbon($validatedData['transactionDate']);

        $bankTransaction = $this->bankTransactionFactory
            ->create(
                $bank,
                $transactionDate,
                $validatedData['description'],
                $validatedData['amount']
            );

        $bankTransaction->setAccount($account);

        // now see if we already have this transaction on record? before saving it
        $bankTransaction = $this->bankTransactionRepository->findOrSave($bankTransaction);

        AccountAuditJob::dispatch($account);

        flash('Bank Transaction \'' . $bankTransaction->getDescription() . '\' created.')->success();

        return redirect()->route('banking.accounts.show', $account->getId());
    }
}
