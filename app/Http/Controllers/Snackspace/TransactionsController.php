<?php

namespace App\Http\Controllers\Snackspace;

use HMS\Entities\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use HMS\Repositories\UserRepository;
use Doctrine\ORM\EntityNotFoundException;
use HMS\Repositories\Snackspace\TransactionRepository;

class TransactionsController extends Controller
{
    /**
     * @var TransactionRepository
     */
    protected $transactionRepository;

    /**
     * @var UserRepository
     */
    protected $userRepository;

    /**
     * Create a new controller instance.
     *
     * @param TransactionRepository $transactionRepository
     * @param UserRepository $userRepository
     */
    public function __construct(TransactionRepository $transactionRepository, UserRepository $userRepository)
    {
        $this->transactionRepository = $transactionRepository;
        $this->userRepository = $userRepository;

        $this->middleware('can:snackspaceTransaction.view.self')->only(['index']);
    }

    /**
     * Display a listing of the resource.
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

            if ($user != \Auth::user() && \Gate::denies('snackspaceTransaction.view.all')) {
                flash('Unauthorized')->error();

                return redirect()->route('home');
            }
        } else {
            $user = \Auth::user();
        }
        if (! $user->getProfile()) {
            flash($user->getFirstname() . ' has no profile')->warning();

            return redirect()->route('home');
        }

        $transactions = $this->transactionRepository->paginateByUser($user);

        return view('snackspace.transaction.index')
            ->with(['user' => $user, 'transactions' => $transactions]);
    }
}
