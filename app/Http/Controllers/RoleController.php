<?php

namespace App\Http\Controllers;

use App\Notifications\Membership\BannedMemberReinstated;
use App\Notifications\Membership\MemberBanned;
use App\Notifications\Membership\MemberTemporarilyBanned;
use App\Notifications\Membership\TemporarilyBannedMemberReinstated;
use Carbon\Carbon;
use Carbon\CarbonInterval;
use HMS\Entities\Role;
use HMS\Entities\User;
use HMS\Repositories\Banking\BankTransactionRepository;
use HMS\Repositories\MetaRepository;
use HMS\Repositories\PermissionRepository;
use HMS\Repositories\RoleRepository;
use HMS\Repositories\RoleUpdateRepository;
use HMS\Repositories\UserRepository;
use HMS\User\Permissions\RoleManager;
use HMS\User\UserManager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleController extends Controller
{
    /**
     * @var RoleManager
     */
    private $roleManager;

    /**
     * @var RoleRepository
     */
    private $roleRepository;

    /**
     * @var PermissionRepository
     */
    private $permissionRepository;

    /**
     * @var UserManager
     */
    private $userManager;

    /**
     * @var UserRepository
     */
    protected $userRepository;

    /**
     * @var BankTransactionRepository
     */
    protected $bankTransactionRepository;

    /**
     * @var MetaRepository
     */
    protected $metaRepository;

    /**
     * @var RoleUpdateRepository
     */
    protected $roleUpdateRepository;

    /**
     * Create a new controller instance.
     *
     * @param RoleManager          $roleManager
     * @param RoleRepository       $roleRepository
     * @param UserManager          $userManager
     * @param PermissionRepository $permissionRepository
     * @param UserRepository $userRepository
     * @param BankTransactionRepository $bankTransactionRepository
     * @param MetaRepository $metaRepository
     * @param RoleUpdateRepository $roleUpdateRepository
     */
    public function __construct(
        RoleManager $roleManager,
        RoleRepository $roleRepository,
        UserManager $userManager,
        PermissionRepository $permissionRepository,
        UserRepository $userRepository,
        BankTransactionRepository $bankTransactionRepository,
        MetaRepository $metaRepository,
        RoleUpdateRepository $roleUpdateRepository
    ) {
        $this->roleManager = $roleManager;
        $this->roleRepository = $roleRepository;
        $this->userManager = $userManager;
        $this->permissionRepository = $permissionRepository;
        $this->userRepository = $userRepository;
        $this->bankTransactionRepository = $bankTransactionRepository;
        $this->metaRepository = $metaRepository;
        $this->roleUpdateRepository = $roleUpdateRepository;

        $this->middleware('canAny:role.view.all,team.view')->only(['index', 'show']);
        $this->middleware('can:role.edit.all')->only(['edit', 'update']);
        $this->middleware('can:role.edit.all')->only('removeUser');

        $this->middleware('can:role.grant.all')->only(['addUser']);
        $this->middleware('can:role.grant.team')->only('addUserToTeam');
        $this->middleware('can:role.grant.team')->only('removeUserFromTeam');

        $this->middleware('can:membership.banMember')->only(['reinstateUser', 'temporaryBanUser', 'banUser']);
        $this->middleware('canAny:profile.view.limited,profile.view.all')->only(['roleUpdates']);
    }

    /**
     * Show the roles.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $roles = $this->roleRepository->findAll();

        $formattedRoles = $this->formatDotNotationList($roles);

        return view('role.index')->with('roles', $formattedRoles);
    }

    /**
     * Show a specific role.
     *
     * @param Role $role the Role
     *
     * @return \Illuminate\Http\Response
     */
    public function show(Role $role)
    {
        $users = $this->userRepository->paginateUsersWithRole($role);

        return view('role.show')->with('role', $role)->with('users', $users);
    }

    /**
     * Show the edit form for a role.
     *
     * @param Role $role the Role
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(Role $role)
    {
        $permissions = $this->permissionRepository->findAll();

        $formattedPermissions = $this->formatDotNotationList($permissions);

        return view('role.edit')->with('role', $role)->with('allPermissions', $formattedPermissions);
    }

    /**
     * Update a specific role.
     *
     * @param Role $role the Role
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Role $role, Request $request)
    {
        $this->validate($request, [
            'displayName' => 'required|string|max:255',
            'description' => 'required',
            'permissions' => 'required|array|nullable',
        ]);

        $this->roleManager->updateRole($role, $request->all());

        return redirect()->route('roles.show', ['role' => $role->getId()]);
    }

    /**
     * Add a specific user to a a specific role.
     *
     * @param Role $role the role
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function addUser(Role $role, Request $request)
    {
        $request['role_name'] = $role->getName();
        $userRepository = $this->userRepository;

        $this->validate($request, [
            'user_id' => [
                'required',
                'exists:HMS\Entities\User,id',
                'bail',
                function ($attribute, $value, $fail) use ($userRepository, $role) {
                    $user = $userRepository->findOneById($value);
                    if ($user->hasRole($role)) {
                        $fail('User already has this Role.');
                    }
                },
            ],
            'role_name' => 'starts_with:user.,tools.,team.', // any but member
        ]);

        $user = $userRepository->findOneById($request->user_id);

        $this->roleManager->addUserToRole($user, $role);

        flash($user->getFullname() . ' added to ' . $role->getDisplayName())->success();

        return back();
    }

    /**
     * Add a specific user to a a specific team role.
     *
     * @param Role $role the role
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function addUserToTeam(Role $role, Request $request)
    {
        $request['role_name'] = $role->getName();
        $userRepository = $this->userRepository;

        $this->validate($request, [
            'user_id' => [
                'required',
                'exists:HMS\Entities\User,id',
                'bail',
                function ($attribute, $value, $fail) use ($userRepository, $role) {
                    $user = $userRepository->findOneById($value);
                    if ($user->hasRole($role)) {
                        $fail('User is already on the team.');
                    }
                },
            ],
            'role_name' => 'starts_with:team.',
        ]);

        $user = $userRepository->findOneById($request->user_id);

        $this->roleManager->addUserToRole($user, $role);

        flash($user->getFullname() . ' added to ' . $role->getDisplayName())->success();

        return back();
    }

    /**
     * Remove a specific user from a specific role.
     *
     * @param Role $role the role
     * @param User $user the user
     *
     * @return \Illuminate\Http\Response
     */
    public function removeUser(Role $role, User $user)
    {
        $this->userManager->removeRoleFromUser($user, $role);
        flash($user->getFullname() . ' removed from ' . $role->getDisplayName())->success();

        return back();
    }

    /**
     * Remove a specific user from a specific team role.
     *
     * @param Role $role the role
     * @param User $user the user
     *
     * @return \Illuminate\Http\Response
     */
    public function removeUserFromTeam(Role $role, User $user)
    {
        if (substr($role->getName(), 0, 5) != 'team.') {
            flash('Not a team role')->warning();

            return redirect()->route('home');
        }

        $this->userManager->removeRoleFromUser($user, $role);
        flash($user->getFullname() . ' removed from ' . $role->getDisplayName())->success();

        return back();
    }

    /**
     * Format role for display.
     *
     * @param \Doctrine\Common\Collections\Collection|array $list
     *
     * @return array
     */
    private function formatDotNotationList($list)
    {
        $formattedList = [];

        foreach ($list as $item) {
            [$category, $name] = explode('.', $item->getName());

            if (! isset($formattedList[$category])) {
                $formattedList[$category] = [];
            }

            $formattedList[$category][] = $item;
        }

        $categories = array_keys($formattedList);
        foreach ($categories as $category) {
            usort($formattedList[$category], function ($a, $b) {
                return strcmp($a->getName(), $b->getName());
            });
        }

        ksort($formattedList);

        return $formattedList;
    }

    /**
     * Reinstate a specific user.
     * Depending on their payment status this could move them back to Current or Ex.
     *
     * @param User $user the user
     *
     * @return \Illuminate\Http\Response
     */
    public function reinstateUser(User $user)
    {
        $reinstatedNotification = null;
        // remove either MEMBER_TEMPORARYBANNED or MEMBER_BANNED role
        if ($user->hasRoleByName(Role::MEMBER_TEMPORARYBANNED)) {
            $this->roleManager->removeUserFromRoleByName($user, Role::MEMBER_TEMPORARYBANNED);
            $reinstatedNotification = new TemporarilyBannedMemberReinstated($user, Auth::user());
        }

        if ($user->hasRoleByName(Role::MEMBER_BANNED)) {
            $this->roleManager->removeUserFromRoleByName($user, Role::MEMBER_BANNED);
            $reinstatedNotification = new BannedMemberReinstated($user, Auth::user());
        }

        // now need to work out if we should reinstate as current or ex Member
        $latestTransaction = $this->bankTransactionRepository->findLatestTransactionByAccount($user->getAccount());

        if (is_null($latestTransaction)) {
            $this->roleManager->addUserToRoleByName($user, Role::MEMBER_PAYMENT);

            flash($user->getFullname() . ' reinstated as Awaiting Payment')->success();

            return redirect()->route('users.admin.show', $user->getId());
        }

        $transactionDate = $latestTransaction->getTransactionDate();

        $revokeDate = Carbon::now();
        $revokeDate->sub(
            CarbonInterval::instance(new \DateInterval($this->metaRepository->get('audit_revoke_interval', 'P2M')))
        );

        if ($transactionDate > $revokeDate) {
            // reinstate as current
            $dob = $user->getProfile()->getDateOfBirth();
            if (is_null($dob)) {
                // no dob assume old enough
                $this->roleManager->addUserToRoleByName($user, Role::MEMBER_CURRENT);
            } elseif ($dob->diffInYears(Carbon::now()) >= 18) { //TODO: meta constants
                $this->roleManager->addUserToRoleByName($user, Role::MEMBER_CURRENT);
            } elseif ($dob->diffInYears(Carbon::now()) >= 16) {
                $this->roleManager->addUserToRoleByName($user, Role::MEMBER_YOUNG);
            } else {
                // should not be here to young
                // TODO: email some one about it
                flash($user->getFullname() . ' not reinstated')->error();

                return redirect()->route('users.admin.show', $user->getId());
            }

            flash($user->getFullname() . ' reinstated as Current Member')->success();
        } else {
            // reinstate as ex
            $this->roleManager->addUserToRoleByName($user, Role::MEMBER_EX);

            flash($user->getFullname() . ' reinstated as Ex Member')->success();
        }

        if (! is_null($reinstatedNotification)) {
            $trusteesTeamRole = $this->roleRepository->findOneByName(Role::TEAM_TRUSTEES);
            $trusteesTeamRole->notify($reinstatedNotification);
        }

        return redirect()->route('users.admin.show', $user->getId());
    }

    /**
     * Temporary Ban a specific user.
     *
     * @param User $user the user
     *
     * @return \Illuminate\Http\Response
     */
    public function temporaryBanUser(User $user)
    {
        // remove all non retained roles (this will include MEMBER_CURRENT and MEMBER_YOUNG)
        foreach ($user->getRoles() as $role) {
            if (! $role->getRetained()) {
                $this->roleManager->removeUserFromRole($user, $role);
            }
        }

        if ($user->hasRoleByName(Role::MEMBER_BANNED)) {
            $this->roleManager->removeUserFromRoleByName($user, Role::MEMBER_BANNED);
        }

        // make temporary banned member
        $this->roleManager->addUserToRoleByName($user, Role::MEMBER_TEMPORARYBANNED);

        flash($user->getFullname() . ' temporarily banned')->success();

        $trusteesTeamRole = $this->roleRepository->findOneByName(Role::TEAM_TRUSTEES);
        $trusteesTeamRole->notify(new MemberTemporarilyBanned($user, Auth::user()));

        return redirect()->route('users.admin.show', $user->getId());
    }

    /**
     * Ban a specific user.
     *
     * @param User $user the user
     *
     * @return \Illuminate\Http\Response
     */
    public function banUser(User $user)
    {
        // remove all non retained roles (this will include MEMBER_CURRENT and MEMBER_YOUNG)
        foreach ($user->getRoles() as $role) {
            if (! $role->getRetained()) {
                $this->roleManager->removeUserFromRole($user, $role);
            }
        }

        if ($user->hasRoleByName(Role::MEMBER_TEMPORARYBANNED)) {
            $this->roleManager->removeUserFromRoleByName($user, Role::MEMBER_TEMPORARYBANNED);
        }

        // make banned member
        $this->roleManager->addUserToRoleByName($user, Role::MEMBER_BANNED);

        $trusteesTeamRole = $this->roleRepository->findOneByName(Role::TEAM_TRUSTEES);
        $trusteesTeamRole->notify(new MemberBanned($user, Auth::user()));

        flash($user->getFullname() . ' banned')->success();

        return redirect()->route('users.admin.show', $user->getId());
    }

    /**
     * View RoleUpdates for a User.
     *
     * @param User $user
     *
     * @return \Illuminate\Http\Response
     */
    public function roleUpdates(User $user)
    {
        $roleUpdates = $this->roleUpdateRepository->paginateByUser($user);

        return view('role.role_updates')
            ->with([
                'user' => $user,
                'roleUpdates' => $roleUpdates,
            ]);
    }
}
