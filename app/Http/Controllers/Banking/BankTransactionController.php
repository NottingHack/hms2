<?php

namespace App\Http\Controllers\Banking;

use App\Http\Controllers\Controller;
use App\Jobs\Banking\AccountAuditJob;
use Carbon\Carbon;
use Doctrine\ORM\EntityNotFoundException;
use HMS\Entities\Banking\BankTransaction;
use HMS\Entities\Banking\BankType;
use HMS\Entities\Snackspace\TransactionType as SnackspaceTransactionType;
use HMS\Entities\User;
use HMS\Factories\Snackspace\TransactionFactory as SnackspaceTransactionFactory;
use HMS\Helpers\Features;
use HMS\Repositories\Banking\AccountRepository;
use HMS\Repositories\Banking\BankRepository;
use HMS\Repositories\Banking\BankTransactionRepository;
use HMS\Repositories\MetaRepository;
use HMS\Repositories\Snackspace\TransactionRepository as SnackspaceTransactionRepository;
use HMS\Repositories\UserRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class BankTransactionController extends Controller
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
     * @var string
     */
    public $accountName;

    /**
     * @var SnackspaceTransactionFactory
     */
    protected $snackspaceTransactionFactory;

    /**
     * @var SnackspaceTransactionRepository
     */
    protected $snackspaceTransactionRepository;

    /**
     * @var Features
     */
    protected $features;

    /**
     * @param BankTransactionRepository $bankTransactionRepository
     * @param UserRepository $userRepository
     * @param AccountRepository $accountRepository
     * @param MetaRepository $metaRepository
     * @param BankRepository $bankRepository
     * @param SnackspaceTransactionFactory $snackspaceTransactionFactory
     * @param SnackspaceTransactionRepository $snackspaceTransactionRepository
     * @param Features $features
     */
    public function __construct(
        BankTransactionRepository $bankTransactionRepository,
        UserRepository $userRepository,
        AccountRepository $accountRepository,
        MetaRepository $metaRepository,
        BankRepository $bankRepository,
        SnackspaceTransactionFactory $snackspaceTransactionFactory,
        SnackspaceTransactionRepository $snackspaceTransactionRepository,
        Features $features
    ) {
        $this->bankTransactionRepository = $bankTransactionRepository;
        $this->userRepository = $userRepository;
        $this->accountRepository = $accountRepository;
        $this->snackspaceTransactionFactory = $snackspaceTransactionFactory;
        $this->snackspaceTransactionRepository = $snackspaceTransactionRepository;
        $this->features = $features;

        $bank = $bankRepository->find($metaRepository->get('so_bank_id'));
        $this->accountNo = $bank->getAccountNumber();
        $this->sortCode = $bank->getSortCode();
        $this->accountName = $bank->getAccountName();

        $this->middleware('can:bankTransactions.view.self')->only(['index']);
        $this->middleware('can:bankTransactions.edit')->only(['edit', 'update']);
        $this->middleware('can:bankTransactions.reconcile')->only(['reconcile', 'match', 'listUnmatched']);
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

            if ($user != Auth::user() &&
                Gate::denies('bankTransactions.view.all') &&
                Gate::denies('bankTransactions.view.limited')
            ) {
                flash('Unauthorized')->error();

                return redirect()->route('home');
            }
        } else {
            $user = Auth::user();
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
            ->with('accountName', $this->accountName)
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
        if (BankType::AUTOMATIC == $bankTransaction->getBank()->getType()) {
            flash('Bank ' . $bankTransaction->getBank()->getName()
                . ' is type Automatic. Transactions can not be edited.')
                ->error();

            return redirect()->route('banking.banks.show', $bankTransaction->getBank()->getId());
        }

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
        if (BankType::AUTOMATIC == $bankTransaction->getBank()->getType()) {
            flash('Bank ' . $bankTransaction->getBank()->getName()
                . ' is type Automatic. Transactions can not be edited.')
                ->error();

            return redirect()->route('banking.banks.show', $bankTransaction->getBank()->getId());
        }

        $validatedData = $request->validate([
            'transactionDate' => 'required|date',
            'description' => 'required|string|max:512',
            'amount' => 'required|integer',
        ]);

        $bankTransaction->setTransactionDate(new Carbon($validatedData['transactionDate']));
        $bankTransaction->setDescription($validatedData['description']);
        if ($bankTransaction->getTransaction()
            && $bankTransaction->getAmount() != $validatedData['amount']) {
            flash('Amount can not be changed once matched for Snackspace')->error();
        } else {
            $bankTransaction->setAmount($validatedData['amount']);
        }

        $this->bankTransactionRepository->save($bankTransaction);

        flash('Transaction updated')->success();

        return redirect()->route('banking.banks.show', $bankTransaction->getBank()->getId());
    }

    /**
     * Show the form to reconcile the specified resource.
     *
     * @param BankTransaction $bankTransaction
     *
     * @return \Illuminate\Http\Response
     */
    public function reconcile(BankTransaction $bankTransaction)
    {
        // TODO: bail if this transaction is all ready matched
        return view('banking.transactions.reconcile')->with(['bankTransaction' => $bankTransaction]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param BankTransaction $bankTransaction
     *
     * @return \Illuminate\Http\Response
     */
    public function match(Request $request, BankTransaction $bankTransaction)
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
            if (BankType::CASH == $bankTransaction->getBank()->getType()
             && $this->features->isDisable('cash_membership_payments')) {
                flash(
                    'Bank ' . $bankTransaction->getBank()->getName()
                    . ' is type Cash and cash membership payments are not currently allowed.'
                )->error();

                return redirect()->route('banking.bank-transactions.unmatched');
            }
            $bankTransaction->setAccount($user->getAccount());
        } elseif ($validatedData['action'] == 'snackspace') {
            // create a new snackspace transaction
            $amount = $bankTransaction->getAmount();
            $stringAmount = money($amount, 'GBP');

            $snackspaceTransaction = $this->snackspaceTransactionFactory->create(
                $user,
                $amount,
                SnackspaceTransactionType::BANK_PAYMENT,
                'Bank Transfer : ' . $stringAmount
            );

            $snackspaceTransaction = $this->snackspaceTransactionRepository
                ->saveAndUpdateBalance($snackspaceTransaction);

            $bankTransaction->setTransaction($snackspaceTransaction);
        }

        $this->bankTransactionRepository->save($bankTransaction);

        // run audit job now the bankTransaction has been saved
        if ($validatedData['action'] == 'membership') {
            AccountAuditJob::dispatch($user->getAccount());
        }

        flash('Transaction updated')->success();

        return redirect()->route('banking.bank-transactions.unmatched');
    }

    /**
     * Listing of all unmatched transations for manual reconcile.
     *
     * @return \Illuminate\Http\Response
     */
    public function listUnmatched()
    {
        $bankTransactions = $this->bankTransactionRepository->paginateUnmatched();

        return view('banking.transactions.listUnmatched')->with(['bankTransactions' => $bankTransactions]);
    }
}
