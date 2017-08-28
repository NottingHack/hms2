<?php

namespace App\Http\Controllers\Snackspace;

use HMS\Entities\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Doctrine\ORM\EntityNotFoundException;
use HMS\Repositories\Snackspace\TransactionRepository;

class TransactionsController extends Controller
{
    /**
     * @var TransactionRepository
     */
    protected $transactionRepository;

    /**
     * @param TransactionRepository $transactionRepository
     */
    public function __construct(TransactionRepository $transactionRepository)
    {
        $this->transactionRepository = $transactionRepository;

        $this->middleware('can:snackspaceTransaction.view.self')->only(['index']);
    }

    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->user) {
            $user = $this->userRepository->find($request->user);
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

        $transactions = $this->transactionRepository->paginateByUser($user);

        return view('snackspace.transaction.index')
            ->with(['user' => $user, 'transactions' => $transactions]);
    }
}
