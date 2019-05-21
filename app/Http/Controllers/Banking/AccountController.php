<?php

namespace App\Http\Controllers\Banking;

use HMS\Entities\User;
use Illuminate\Http\Request;
use HMS\Entities\Banking\Account;
use App\Http\Controllers\Controller;
use HMS\Repositories\UserRepository;
use HMS\Factories\Banking\AccountFactory;
use HMS\Repositories\Banking\AccountRepository;

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
     * Create a new controller instance.
     *
     * @param AccountRepository $accountRepository
     * @param UserRepository $userRepository
     */
    public function __construct(
        AccountRepository $accountRepository,
        AccountFactory $accountFactory,
        UserRepository $userRepository
    ) {
        $this->accountRepository = $accountRepository;
        $this->accountFactory = $accountFactory;
        $this->userRepository = $userRepository;

        $this->middleware('can:profile.view.all')->only(['listJoint', 'show']);
        $this->middleware('can:profile.edit.all')->only(['linkUser', 'unlinkUser']);
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
        // $bankTransactions = $this->bankTransactionRepository->paginateByAccount($user->getAccount(), 10);

        return view('banking.accounts.show')
            ->with('account', $account);
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
        $valiidatedDate = $request->validate([
            'user_id' => [
                'required',
                'exists:HMS\Entities\User,id',
            ],
        ]);

        $user = $this->userRepository->findOneById($valiidatedDate['user_id']);

        // TODO: As this will changes a Users account_id, we will orphan the old Account ref
        // if there are no bank_transaction against the ref we should be safe to delete it?
        $oldAccount = $user->getAccount();

        $user->setAccount($account);
        $this->userRepository->save($user);

        // TODO: fire some user account changed event?

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
        $valiidatedDate = $request->validate([
            'user_id' => [
                'required',
                'exists:HMS\Entities\User,id',
            ],
            'new-account' => 'required|boolean',
            'existing-account' => 'required_if:new-account,false|exists:HMS\Entities\Banking\Account,id',
        ]);

        $user = $this->userRepository->findOneById($valiidatedDate['user_id']);

        if ($valiidatedDate['new-account']) {
            $newAccount = $this->accountFactory->createNewAccount();
        } else {
            $newAccount = $this->accountRepository->findOneById($valiidatedDate['existing-account']);
        }

        $user->setAccount($newAccount);
        $this->userRepository->save($user);

        // TODO: fire some user account changed event?

        flash($user->getFullname() . ' un-linked from Account.')->success();

        return redirect()->route('banking.accounts.show', $account->getId());
    }
}
