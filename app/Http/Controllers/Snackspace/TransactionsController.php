<?php

namespace App\Http\Controllers\Snackspace;

use App\Http\Controllers\Controller;
use Doctrine\ORM\EntityNotFoundException;
use HMS\Entities\Snackspace\TransactionType;
use HMS\Entities\User;
use HMS\Factories\Snackspace\TransactionFactory;
use HMS\Repositories\Snackspace\TransactionRepository;
use HMS\Repositories\UserRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

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
     * @var TransactionFactory
     */
    protected $transactionFactory;

    /**
     * Create a new controller instance.
     *
     * @param TransactionRepository $transactionRepository
     * @param UserRepository $userRepository
     * @param TransactionFactory $transactionFactory
     */
    public function __construct(
        TransactionRepository $transactionRepository,
        UserRepository $userRepository,
        TransactionFactory $transactionFactory
    ) {
        $this->transactionRepository = $transactionRepository;
        $this->userRepository = $userRepository;
        $this->transactionFactory = $transactionFactory;

        $this->middleware('feature:snackspace');
        $this->middleware('can:snackspace.transaction.view.self')->only(['index']);
        $this->middleware('can:snackspace.transaction.create.all')->only(['create', 'store']);
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

            if ($user != Auth::user() && Gate::denies('snackspace.transaction.view.all')) {
                flash('Unauthorized')->error();

                return redirect()->route('home');
            }
        } else {
            $user = Auth::user();
        }
        if (! $user->getProfile()) {
            flash($user->getFirstname() . ' has no profile')->warning();

            return redirect()->route('home');
        }

        $transactions = $this->transactionRepository->paginateByUser($user);

        return view('snackspace.transaction.index')
            ->with(['user' => $user, 'transactions' => $transactions]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param User $user
     *
     * @return \Illuminate\Http\Response
     */
    public function create(User $user)
    {
        return view('snackspace.transaction.create')
            ->with('user', $user);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param User $user
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store(User $user, Request $request)
    {
        $validatedData = $request->validate([
            'description' => 'required|string|max:512',
            'amount' => 'required|integer',
        ]);

        $transaction = $this->transactionFactory
            ->create(
                $user,
                $validatedData['amount'],
                TransactionType::MANUAL,
                $validatedData['description']
            );
        $this->transactionRepository->saveAndUpdateBalance($transaction);
        flash('Transaction added.')->success();

        return redirect()->route('users.snackspace.transactions', $user->getId());
    }
}
