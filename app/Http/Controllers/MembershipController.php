<?php

namespace App\Http\Controllers;

use App\Events\MembershipInterestRegistered;
use App\Jobs\Banking\AccountAuditJob;
use App\Mail\MembershipDetailsApproved;
use App\Mail\MembershipDetailsRejected;
use App\Notifications\NewMemberApprovalNeeded;
use Carbon\Carbon;
use HMS\Entities\Invite;
use HMS\Entities\Membership\RejectedLog;
use HMS\Entities\Role;
use HMS\Entities\User;
use HMS\Factories\Banking\AccountFactory;
use HMS\Repositories\Banking\AccountRepository;
use HMS\Repositories\Banking\BankRepository;
use HMS\Repositories\Membership\RejectedLogRepository;
use HMS\Repositories\MetaRepository;
use HMS\Repositories\RoleRepository;
use HMS\Repositories\UserRepository;
use HMS\User\Permissions\RoleManager;
use HMS\User\ProfileManager;
use HMS\User\UserManager;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class MembershipController extends Controller
{
    /**
     * @var MetaRepository
     */
    protected $metaRepository;

    /**
     * @var AccountFactory
     */
    protected $accountFactory;

    /**
     * @var UserRepository
     */
    protected $userRepository;

    /**
     * @var RoleManager
     */
    protected $roleManager;

    /**
     * @var AccountRepository
     */
    protected $accountRepository;

    /**
     * @var UserManager
     */
    protected $userManager;

    /**
     * @var ProfileManager
     */
    protected $profileManager;

    /**
     * @var RoleRepository
     */
    protected $roleRepository;

    /**
     * @var BankRepository
     */
    protected $bankRepository;

    /**
     * @var RejectedLogRepository
     */
    protected $rejectedLogRepository;

    /**
     * Create a new controller instance.
     *
     * @param MetaRepository    $metaRepository
     * @param AccountFactory    $accountFactory
     * @param UserRepository    $userRepository
     * @param RoleManager       $roleManager
     * @param AccountRepository $accountRepository
     * @param UserManager       $userManager
     * @param ProfileManager    $profileManager
     * @param RoleRepository    $roleRepository
     * @param BankRepository    $bankRepository
     * @param RejectedLogRepository $rejectedLogRepository
     */
    public function __construct(
        MetaRepository $metaRepository,
        AccountFactory $accountFactory,
        UserRepository $userRepository,
        RoleManager $roleManager,
        AccountRepository $accountRepository,
        UserManager $userManager,
        ProfileManager $profileManager,
        RoleRepository $roleRepository,
        BankRepository $bankRepository,
        RejectedLogRepository $rejectedLogRepository
    ) {
        $this->metaRepository = $metaRepository;
        $this->accountFactory = $accountFactory;
        $this->userRepository = $userRepository;
        $this->roleManager = $roleManager;
        $this->accountRepository = $accountRepository;
        $this->userManager = $userManager;
        $this->profileManager = $profileManager;
        $this->roleRepository = $roleRepository;
        $this->bankRepository = $bankRepository;
        $this->rejectedLogRepository = $rejectedLogRepository;

        $this->middleware('can:membership.approval')
            ->only(['index', 'showDetailsForApproval', 'approveDetails', 'rejectDetails']);
        $this->middleware('can:membership.updateDetails')->only(['editDetails', 'updateDetails']);
        $this->middleware('can:search.invites')->only(['invitesResend']);
    }

    /**
     * Show a list of members awaiting approval.
     *
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function index()
    {
        $approvalRole = $this->roleRepository->findOneByName(Role::MEMBER_APPROVAL);
        $users = $approvalRole->getUsers();

        $approvals = [];

        foreach ($users as $user) {
            $rejectedLogs = $this->rejectedLogRepository->findByUser($user);
            $approvals[] = [
                $user,
                array_pop($rejectedLogs),
            ];
        }

        $pageName = 'page';
        $page = LengthAwarePaginator::resolveCurrentPage($pageName);
        $perPage = 15;

        $approvalsPagination = new LengthAwarePaginator(
            array_slice($approvals, ($page - 1) * $perPage, $perPage),
            count($approvals),
            $perPage,
            $page,
            [
                'path' => LengthAwarePaginator::resolveCurrentPath(),
                'pageName' => $pageName,
            ]
        );

        return view('membership.index')
            ->with('approvals', $approvalsPagination);
    }

    /**
     * Show the membership approval form.
     *
     * @param User $user
     *
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function showDetailsForApproval(User $user)
    {
        if (! $user->hasRoleByName(Role::MEMBER_APPROVAL)) {
            flash('User does not require approval')->warning();

            return redirect()->route('home');
        }

        $rejectedLogs = $this->rejectedLogRepository->findByUser($user);

        return view('membership.showDetails')
            ->with('user', $user)
            ->with('rejectedLogs', $rejectedLogs);
    }

    /**
     * Approve new member details.
     * Now setup bank account ref, either new or linked, and email member with standing order setup details
     * (possible deal with young hacker stuff here).
     *
     * @param User $user
     * @param Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function approveDetails(User $user, Request $request)
    {
        if (! $user->hasRoleByName(Role::MEMBER_APPROVAL)) {
            return redirect()->route('home');
        }

        // Check that it hasn't already been rejected by someone who got there first.
        $rejectedLogs = $this->rejectedLogRepository->findByUser($user);
        if (! empty($rejectedLogs)) {
            $rejectedLog = array_pop($rejectedLogs);
            if (! $rejectedLog->getUserUpdatedAt()) {
                flash('This profile has already been rejected but not yet updated by the user.')->error();

                return redirect()->route('membership.index');
            }
        }

        $this->validate($request, [
            'new-account' => 'required|boolean',
            'existing-account' => 'required_if:new-account,false|exists:HMS\Entities\Banking\Account,id',
        ]);

        if ($request['new-account']) {
            $account = $this->accountFactory->createNewAccount();
        } else {
            $account = $this->accountRepository->findOneById($request['existing-account']);
        }

        $user->setAccount($account);
        $this->userRepository->save($user);

        // move user form Role::MEMBER_APPROVAL to Role::MEMBER_PAYMENT
        $this->roleManager->removeUserFromRoleByName($user, Role::MEMBER_APPROVAL);
        $this->roleManager->addUserToRoleByName($user, Role::MEMBER_PAYMENT);

        // notify the user
        Mail::to($user)->send(new MembershipDetailsApproved($user, $this->metaRepository, $this->bankRepository));

        if (! $request['new-account']) {
            AccountAuditJob::dispatch($account);
        }

        // TODO: if Role::MEMBER_APPROVAL users count is now 0 notify membship slack

        flash('Member approved, thank you.')->success();

        return redirect()->route('membership.index');
    }

    /**
     * Reject new member details.
     * Email the member asking them to update Details as noted.
     *
     * @param User $user
     * @param Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function rejectDetails(User $user, Request $request)
    {
        if (! $user->hasRoleByName(Role::MEMBER_APPROVAL)) {
            return redirect()->route('home');
        }

        $this->validate($request, [
            'reason' => 'required|string|max:250',
        ]);

        // LogDetailsRejectedJob
        $_rejectedLog = new RejectedLog();
        $_rejectedLog->setUser($user);
        $_rejectedLog->setReason($request['reason']);
        $_rejectedLog->setRejectedBy(Auth::user());

        $this->rejectedLogRepository->save($_rejectedLog);

        Mail::to($user)->send(new MembershipDetailsRejected($user, $request['reason']));

        flash('Member notified, thank you.')->success();

        return redirect()->route('membership.index');
    }

    /**
     * Show the user a form to allow updating of there details and request anoter review.
     *
     * @param User $user
     *
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function editDetails(User $user)
    {
        if (! $user->hasRoleByName(Role::MEMBER_APPROVAL)) {
            return redirect()->route('home');
        }

        $rejectedLogs = $this->rejectedLogRepository->findByUser($user);
        $rejectedLog = array_pop($rejectedLogs);

        return view('membership.editDetails')
            ->with('user', $user)
            ->with('rejectedLog', optional($rejectedLog)->getUserUpdatedAt() ? null : $rejectedLog);
    }

    /**
     * Store the updated users details and request another review from membership team.
     *
     * @param User $user
     * @param Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function updateDetails(User $user, Request $request)
    {
        if (! $user->hasRoleByName(Role::MEMBER_APPROVAL)) {
            return redirect()->route('home');
        }

        $validatedData = $request->validate([
            'firstname' => 'required|max:255',
            'lastname' => 'required|max:255',
            'address1' => 'required|max:100',
            'address2' => 'nullable|max:100',
            'address3' => 'nullable|max:100',
            'addressCity' => 'required|max:100',
            'addressCounty' => 'required|max:100',
            'addressPostcode' => 'required|max:10',
            'contactNumber' => 'required|max:50',
            'dateOfBirth' => 'sometimes|nullable|date_format:Y-m-d',
        ]);

        $user = $this->userManager->updateFromRequest($user, $validatedData);
        $user = $this->profileManager->updateUserProfileFromRequest($user, $validatedData);

        // Update last rejectedLog if there is one
        $rejectedLogs = $this->rejectedLogRepository->findByUser($user);
        if (! empty($rejectedLogs)) {
            $rejectedLog = array_pop($rejectedLogs);
            $rejectedLog->setUserUpdatedAt(Carbon::now());
            $this->rejectedLogRepository->save($rejectedLog);
        }

        $membershipTeamRole = $this->roleRepository->findOneByName(Role::TEAM_MEMBERSHIP);
        $membershipTeamRole->notify(new NewMemberApprovalNeeded($user, true));

        flash('Your details have been updated and another review requested, thank you.')->success();

        return redirect()->route('home');
    }

    /**
     * Resend an invite.
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function invitesResend(Invite $invite)
    {
        event(new MembershipInterestRegistered($invite));

        flash('Invite re-sent.')->success();

        return back();
    }
}
