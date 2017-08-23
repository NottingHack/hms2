<?php

namespace App\Http\Controllers\Banking;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use HMS\Repositories\UserRepository;
use HMS\Entities\Banking\BankTransaction;
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
     * @param BankTransactionRepository $bankTransactionRepository
     * @param UserRepository $userRepository
     */
    public function __construct(BankTransactionRepository $bankTransactionRepository, UserRepository $userRepository)
    {
        $this->bankTransactionRepository = $bankTransactionRepository;
        $this->userRepository = $userRepository;

        $this->middleware('can:bankTransactions.view.self')->only(['index']);
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
}
