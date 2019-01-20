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
    public function __construct(
        BankTransactionRepository $bankTransactionRepository,
        UserRepository $userRepository,
        AccountRepository $accountRepository
    ) {
        $this->bankTransactionRepository = $bankTransactionRepository;
        $this->userRepository = $userRepository;
        $this->accountRepository = $accountRepository;

        $this->middleware('can:bankTransactions.view.self')->only(['index']);
        $this->middleware('can:bankTransactions.reconcile')->only(['edit', 'update', 'listUnmatched']);
    }

    /**
     * BankTransactions for a given users account.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->user) {
            $user = $this->userRepository->findOneById($request->user);
            if (is_null($user)) {
                throw EntityNotFoundException::fromClassNameAndIdentifier(User::class, ['id' => $request->user]);
            }

            if ($user != \Auth::user() && \Gate::denies('bankTransactions.view.all')) {
                flash('Unauthorized')->error();

                return redirect()->route('home');
            }
        } else {
            $user = \Auth::user();
        }

        if (is_null($user->getAccount())) {
            flash('No Account for User')->error();

            return redirect()->route('home');
        }

        $bankTransactions = $this->bankTransactionRepository->paginateByAccount($user->getAccount(), 10);

        return view('bankTransactions.index')
            ->with(['user' => $user, 'bankTransactions' => $bankTransactions]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  BankTransaction  $bankTransaction
     * @return \Illuminate\Http\Response
     */
    public function edit(BankTransaction $bankTransaction)
    {
        return view('bankTransactions.edit')->with(['bankTransaction' => $bankTransaction]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  BankTransaction  $bankTransaction
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, BankTransaction $bankTransaction)
    {
        $account = $this->accountRepository->findOneById($request['existing-account']);
        $bankTransaction->setAccount($account);
        $this->bankTransactionRepository->save($bankTransaction);

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

        return view('bankTransactions.listUnmatched')->with(['bankTransactions' => $bankTransactions]);
    }
}
