<?php

namespace App\Http\Controllers\Banking;

use App\Http\Controllers\Controller;
use App\Jobs\Banking\AccountAuditJob;
use HMS\Entities\Banking\Account;
use HMS\Entities\User;
use HMS\Factories\Banking\AccountFactory;
use HMS\Repositories\Banking\AccountRepository;
use HMS\Repositories\Banking\BankTransactionRepository;
use HMS\Repositories\UserRepository;
use Illuminate\Http\Request;

class AccountController extends Controller
{
    /**
     * @var AccountRepository
     */
    protected $accountRepository;

    /**
     * @var AccountFactory
     */
    protected $accountFactory;

    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * @var BankTransactionRepository
     */
    protected $bankTransactionRepository;

    /**
     * Create a new controller instance.
     *
     * @param AccountRepository $accountRepository
     * @param AccountFactory $accountFactory
     * @param UserRepository $userRepository
     * @param BankTransactionRepository $bankTransactionRepository
     */
    public function __construct(
        AccountRepository $accountRepository,
        AccountFactory $accountFactory,
        UserRepository $userRepository,
        BankTransactionRepository $bankTransactionRepository
    ) {
        $this->accountRepository = $accountRepository;
        $this->accountFactory = $accountFactory;
        $this->userRepository = $userRepository;
        $this->bankTransactionRepository = $bankTransactionRepository;

        $this->middleware('canAny:bankTransactions.view.limited,bankTransactions.view.all')
            ->only(['listJoint', 'show']);
        $this->middleware('canAny:profile.edit.limited,profile.edit.all')->only(['linkUser', 'unlinkUser']);
    }

    /**
     * Display a listing of the join Acounts.
     *
     * @return \Illuminate\Http\Response
     */
    public function listJoint()
    {
        $joinAccounts = $this->accountRepository->paginateJointAccounts();

        return view('banking.accounts.joint')
            ->with('jointAccounts', $joinAccounts);
    }

    /**
     * Display the specified Acount.
     *
     * @param Account  $account
     *
     * @return \Illuminate\Http\Response
     */
    public function show(Account $account)
    {
        $bankTransactions = $this->bankTransactionRepository->paginateByAccount($account, 10);

        return view('banking.accounts.show')
            ->with('account', $account)
            ->with('bankTransactions', $bankTransactions);
    }

    /**
     * Link a given User with this Account.
     *
     * @param Account $account
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function linkUser(Account $account, Request $request)
    {
        $validatedData = $request->validate([
            'user_id' => [
                'required',
                'exists:HMS\Entities\User,id',
            ],
        ]);

        $user = $this->userRepository->findOneById($validatedData['user_id']);

        // TODO: As this will changes a Users account_id, we will orphan the old Account ref
        // if there are no bank_transaction against the ref we should be safe to delete it?
        $oldAccount = $user->getAccount();

        $user->setAccount($account);
        $this->userRepository->save($user);

        // TODO: fire some user account changed event?

        // run audit job for new linked user
        AccountAuditJob::dispatch($account);

        flash($user->getFullname() . ' linked to Account.')->success();

        return redirect()->route('banking.accounts.show', $account->getId());
    }

    /**
     * Unlink a given User with this Account.
     *
     * @param Account $account
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function unlinkUser(Account $account, Request $request)
    {
        $validatedData = $request->validate([
            'user_id' => [
                'required',
                'exists:HMS\Entities\User,id',
            ],
            'new-account' => 'required|boolean',
            'existing-account' => 'required_if:new-account,false|exists:HMS\Entities\Banking\Account,id',
        ]);

        $user = $this->userRepository->findOneById($validatedData['user_id']);

        if ($validatedData['new-account']) {
            $newAccount = $this->accountFactory->createNewAccount();
        } else {
            $newAccount = $this->accountRepository->findOneById($validatedData['existing-account']);
        }

        $user->setAccount($newAccount);
        $this->userRepository->save($user);

        // TODO: fire some user account changed event?

        // TODO: should we run audit job for unlinked user?
        // AccountAuditJob::dispatch($newAaccount);

        flash($user->getFullname() . ' un-linked from Account.')->success();

        return redirect()->route('banking.accounts.show', $account->getId());
    }
}
