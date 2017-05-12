<?php

namespace App\Http\Controllers;

use App\Http\Requests;
use HMS\Entities\Role;
use HMS\Entities\User;
use HMS\User\UserManager;
use HMS\User\ProfileManager;
use Illuminate\Http\Request;
use HMS\Repositories\MetaRepository;
use HMS\Repositories\RoleRepository;
use HMS\Repositories\UserRepository;
use HMS\User\Permissions\RoleManager;
use App\Mail\MembershipDetailsApproved;
use App\Mail\MembershipDetailsRejected;
use HMS\Factories\Banking\AccountFactory;
use App\Notifications\NewMemberApprovalNeeded;
use HMS\Repositories\Banking\AccountRepository;

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
     * Create a new controller instance.
     */
    public function __construct(MetaRepository $metaRepository,
        AccountFactory $accountFactory,
        UserRepository $userRepository,
        RoleManager $roleManager,
        AccountRepository $accountRepository,
        UserManager $userManager,
        ProfileManager $profileManager,
        RoleRepository $roleRepository)
    {
        $this->metaRepository = $metaRepository;
        $this->accountFactory = $accountFactory;
        $this->userRepository = $userRepository;
        $this->roleManager = $roleManager;
        $this->accountRepository = $accountRepository;
        $this->userManager = $userManager;

        $this->middleware('can:membership.approval')->only(['showDetailsForApproval', 'approveDetails', 'rejectDetails']);
        $this->middleware('can:membership.updateDetails')->only(['editDetails', 'updateDetails']);
        $this->profileManager = $profileManager;
        $this->roleRepository = $roleRepository;
    }

    /**
     * Show the membership approval form.
     *
     * @param  User   $user
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function showDetailsForApproval(User $user)
    {
        if ($user->hasRoleByName(Role::MEMBER_APPROVAL)) {
            return view('membership.showDetails')->with(
                'user', $user)->with(
                'subject', $this->metaRepository->get('reject_details_subject', 'Nottingham Hackspace: Issue with contact details')

                );
        }

        flash('User does not require approval')->warning();

        return redirect()->route('home');
    }

    /**
     * Approve new member details.
     * Now setup bank account ref, either new or linked, and email member with standing order setup details
     * (possible deal with young hacker stuff here)
     *
     * @param  User    $user
     * @param  Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function approveDetails(User $user, Request $request)
    {
        if ( ! $user->hasRoleByName(Role::MEMBER_APPROVAL)) {
            return redirect()->route('home');
        }

        $this->validate($request, [
            'new-account' => 'required|boolean',
            'existing-account' => 'required_if:new-account,false|exists:HMS\Entities\Banking\Account,id',
        ]);

        if ($request['new-account']){
            $account = $this->accountFactory->createNewAccount();
        } else {
            $account = $this->accountRepository->find($request['existing-account']);
        }

        $user->setAccount($account);
        $this->userRepository->save($user);

        // move user form Role::MEMBER_APPROVAL to Role::MEMBER_PAYMENT
        $this->roleManager->removeUserFromRoleByName($user, Role::MEMBER_APPROVAL);
        $this->roleManager->addUserToRoleByName($user, Role::MEMBER_PAYMENT);

        // notify the user
        \Mail::to($user)->send(new MembershipDetailsApproved($user, $this->metaRepository));

        flash('Member approved, thank you.')->success();

        return redirect()->route('home');
    }

    /**
     * Reject new member details.
     * Email the member asking them to update Details as noted.
     *
     * @param  User    $user
     * @param  Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function rejectDetails(User $user, Request $request)
    {
        if ( ! $user->hasRoleByName(Role::MEMBER_APPROVAL)) {
            return redirect()->route('home');
        }

        $this->validate($request, [
            'reason' => 'required|string|max:500',
        ]);

        \Mail::to($user)->send(new MembershipDetailsRejected($user, $request['reason']));

        flash('Member notified, thank you.')->success();

        return redirect()->route('home');

    }

    /**
     * Show the user a form to allow updating of there details and request anoter review.
     * @param  User   $user
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function editDetails(User $user)
    {
        if ( ! $user->hasRoleByName(Role::MEMBER_APPROVAL)) {
            return redirect()->route('home');
        }

        return view('membership.editDetails', [
            'id' => $user->getId(),
            'firstname' => $user->getFirstname(),
            'lastname' => $user->getLastname(),
            'address1' => $user->getProfile()->getAddress1(),
            'address2' => $user->getProfile()->getAddress2(),
            'address3' => $user->getProfile()->getAddress3(),
            'addressCity' => $user->getProfile()->getAddressCity(),
            'addressCounty' => $user->getProfile()->getAddressCounty(),
            'addressPostcode' => $user->getProfile()->getAddressPostcode(),
            'contactNumber' => $user->getProfile()->getContactNumber(),
            'dateOfBirth' => $user->getProfile()->getDateOfBirth(),
            ]);
    }

    /**
     * Store the updated users details and request another review from membership team.
     *
     * @param  User   $user
     * @param  Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function updateDetails(User $user, Request $request)
    {
        if ( ! $user->hasRoleByName(Role::MEMBER_APPROVAL)) {
            return redirect()->route('home');
        }

        $this->validate($request, [
            'firstname' => 'required|max:255',
            'lastname' => 'required|max:255',
            'address1' => 'required|max:100',
            'address2' => 'nullable|max:100',
            'address3' => 'nullable|max:100',
            'addressCity' => 'required|max:100',
            'addressCounty' => 'required|max:100',
            'addressPostcode' => 'required|max:10',
            'contactNumber' => 'required|max:50',
            'dateOfBirth' => 'nullable|date',
        ]);

        $user = $this->userManager->updateFromRequest($user, $request);
        $user = $this->profileManager->updateUserProfileFromRequest($user, $request);

        $membershipTeamRole = $this->roleRepository->findByName(Role::TEAM_MEMBERSHIP);
        $membershipTeamRole->notify(new NewMemberApprovalNeeded($user, true));

        flash('Your detials have been updated and another review requested, thank you.')->success();

        return redirect()->route('home');
    }
}
