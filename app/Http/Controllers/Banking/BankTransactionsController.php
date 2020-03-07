<?php

namespace App\Http\Controllers\Banking;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;
use HMS\Repositories\MetaRepository;
use HMS\Repositories\UserRepository;
use App\Jobs\Banking\AccountAuditJob;
use HMS\Entities\Banking\BankTransaction;
use HMS\Entities\Snackspace\TransactionType;
use HMS\Repositories\Banking\BankRepository;
use HMS\Repositories\Banking\AccountRepository;
use HMS\Factories\Snackspace\TransactionFactory;
use HMS\Repositories\Snackspace\TransactionRepository;
use HMS\Repositories\Banking\BankTransactionRepository;

class BankTransactionsController extends Controller
{
    /**
     * @var BankTransactionRepository
     */
    protected $bankTransactionRepository;

    /**
     * @var UserRepository
     */
    protected $userRepository;

    /**
     * @var AccountRepository
     */
    protected $accountRepository;

    /**
     * @var string
     */
    public $accountNo;

    /**
     * @var string
     */
    public $sortCode;

    /**
     * @var TransactionFactory
     */
    protected $transactionFactory;

    /**
     * @var TransactionRepository
     */
    protected $transactionRepository;

    /**
     * @param BankTransactionRepository $bankTransactionRepository
     * @param UserRepository $userRepository
     * @param AccountRepository $accountRepository
     * @param MetaRepository $metaRepository
     * @param BankRepository $bankRepository
     * @param TransactionFactory $transactionFactory
     * @param TransactionRepository $transactionRepository
     */
    public function __construct(
        BankTransactionRepository $bankTransactionRepository,
        UserRepository $userRepository,
        AccountRepository $accountRepository,
        MetaRepository $metaRepository,
        BankRepository $bankRepository,
        TransactionFactory $transactionFactory,
        TransactionRepository $transactionRepository
    ) {
        $this->bankTransactionRepository = $bankTransactionRepository;
        $this->userRepository = $userRepository;
        $this->accountRepository = $accountRepository;
        $this->transactionFactory = $transactionFactory;
        $this->transactionRepository = $transactionRepository;

        $bank = $bankRepository->find($metaRepository->get('so_bank_id'));
        $this->accountNo = $bank->getAccountNumber();
        $this->sortCode = $bank->getSortCode();

        $this->middleware('can:bankTransactions.view.self')->only(['index']);
        $this->middleware('can:bankTransactions.reconcile')->only(['edit', 'update', 'listUnmatched']);
    }

    /**
     * BankTransactions for a given users account.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->user) {
            $user = $this->userRepository->findOneById($request->user);
            if (is_null($user)) {
                throw EntityNotFoundException::fromClassNameAndIdentifier(User::class, ['id' => $request->user]);
            }

            if ($user != \Auth::user() &&
                \Gate::denies('bankTransactions.view.all') &&
                \Gate::denies('bankTransactions.view.limited')
            ) {
                flash('Unauthorized')->error();

                return redirect()->route('home');
            }
        } else {
            $user = \Auth::user();
        }

        if (is_null($user->getAccount())) {
            flash('No Account for ' . $user->getFirstname())->error();

            return redirect()->route('home');
        }

        $paymentRef = $user->getAccount()->getPaymentRef();

        $bankTransactions = $this->bankTransactionRepository->paginateByAccount($user->getAccount(), 10);

        return view('banking.transactions.index')
            ->with('user', $user)
            ->with('bankTransactions', $bankTransactions)
            ->with('accountNo', $this->accountNo)
            ->with('sortCode', $this->sortCode)
            ->with('paymentRef', $paymentRef);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param BankTransaction $bankTransaction
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(BankTransaction $bankTransaction)
    {
        // TODO: bail if this transaction is all ready matched
        return view('banking.transactions.edit')->with(['bankTransaction' => $bankTransaction]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param BankTransaction $bankTransaction
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, BankTransaction $bankTransaction)
    {
        $validatedData = $request->validate([
            'action' => [
                'required',
                Rule::in(['membership', 'snackspace']),
            ],
            'user_id' => 'required|exists:HMS\Entities\User,id',
        ]);

        $user = $this->userRepository->findOneById($validatedData['user_id']);

        if ($validatedData['action'] == 'membership') {
            $bankTransaction->setAccount($user->getAccount());
        } elseif ($validatedData['action'] == 'snackspace') {
            // create a new snackspace transaction
            $amount = $bankTransaction->getAmount();
            $stringAmount = money($amount, 'GBP');

            $snackspaceTransaction = $this->transactionFactory->create(
                $user,
                $amount,
                TransactionType::BANK_PAYMENT,
                'Bank Transfer : ' . $stringAmount
            );

            $snackspaceTransaction = $this->transactionRepository->saveAndUpdateBalance($snackspaceTransaction);

            $bankTransaction->setTransaction($snackspaceTransaction);
        }

        $this->bankTransactionRepository->save($bankTransaction);

        // run audit job now the bankTransaction has been saved
        if ($validatedData['action'] == 'membership') {
            AccountAuditJob::dispatch($user->getAccount());
        }

        flash('Transaction updated')->success();

        return redirect()->route('bank-transactions.unmatched');
    }

    /**
     * Listing of all unmatched transations for manual reconcile.
     *
     * @return \Illuminate\Http\Response
     */
    public function listUnmatched()
    {
        $bankTransactions = $this->bankTransactionRepository->paginateByAccount(null);

        return view('banking.transactions.listUnmatched')->with(['bankTransactions' => $bankTransactions]);
    }
}
