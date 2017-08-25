<?php

namespace App\Http\Controllers\Banking;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use HMS\Repositories\UserRepository;
use HMS\Entities\Banking\BankTransaction;
use HMS\Repositories\Banking\AccountRepository;
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
     * @param BankTransactionRepository $bankTransactionRepository
     * @param UserRepository $userRepository
     * @param AccountRepository $accountRepository
     */
    public function __construct(BankTransactionRepository $bankTransactionRepository,
        UserRepository $userRepository,
        AccountRepository $accountRepository)
    {
        $this->bankTransactionRepository = $bankTransactionRepository;
        $this->userRepository = $userRepository;
        $this->accountRepository = $accountRepository;

        $this->middleware('can:bankTransactions.view.self')->only(['index']);
        $this->middleware('can:bankTransactions.reconcile')->only(['edit', 'update', 'listUnmatched']);
    }

    /**
     * BankTransactions for a given users account.
     *
     * @param  null|user $user
     * @return \Illuminate\Http\Response
     */
    public function index($user = null)
    {
        if (is_null($user)) {
            $_user = \Auth::user();
        } else {
            $_user = $this->userRepository->find($user);
            if (is_null($_user)) {
                throw EntityNotFoundException::fromClassNameAndIdentifier(User::class, ['id' => $user]);
            }
        }

        if ($_user != \Auth::user() && \Gate::denies('bankTransactions.view.all')) {
            flash('Unauthorized')->error();

            return redirect()->route('home');
        }

        if (is_null($_user->getAccount())) {
            flash('No Account for User')->error();

            return redirect()->route('home');
        }

        $bankTransactions = $this->bankTransactionRepository->paginateByAccount($_user->getAccount(), 10);

        return view('bankTransactions.index')
            ->with(['user' => $_user, 'bankTransactions' => $bankTransactions]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  BankTransaction  $bank_transaction
     * @return \Illuminate\Http\Response
     */
    public function edit(BankTransaction $bank_transaction)
    {
        return view('bankTransactions.edit')->with(['bankTransaction' => $bank_transaction]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  BankTransaction  $bank_transaction
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, BankTransaction $bank_transaction)
    {
        $account = $this->accountRepository->find($request['existing-account']);
        $bank_transaction->setAccount($account);
        $this->bankTransactionRepository->save($bank_transaction);

        flash('Transaction updated')->success();

        return redirect()->route('bankTransactions.unmatched');
    }

    /**
     * Listing of all unmatched transations for manual reconcile.
     *
     * @return \Illuminate\Http\Response
     */
    public function listUnmatched()
    {
        $bankTransactions = $this->bankTransactionRepository->paginateByAccount(null);

        return view('bankTransactions.listUnmatched')->with(['bankTransactions' => $bankTransactions]);
    }
}
