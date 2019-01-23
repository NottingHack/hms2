<?php

namespace App\Http\Controllers;

use HMS\Entities\Role;
use HMS\Entities\User;
use HMS\User\UserManager;
use Illuminate\Http\Request;
use HMS\Repositories\RoleRepository;
use HMS\Repositories\UserRepository;
use HMS\User\Permissions\RoleManager;
use HMS\Repositories\PermissionRepository;
use Doctrine\Common\Collections\ArrayCollection;

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
     * Create a new controller instance.
     *
     * @param RoleManager          $roleManager
     * @param RoleRepository       $roleRepository
     * @param UserManager          $userManager
     * @param PermissionRepository $permissionRepository
     */
    public function __construct(
        RoleManager $roleManager,
        RoleRepository $roleRepository,
        UserManager $userManager,
        PermissionRepository $permissionRepository,
        UserRepository $userRepository
    ) {
        $this->roleManager = $roleManager;
        $this->roleRepository = $roleRepository;
        $this->userManager = $userManager;
        $this->permissionRepository = $permissionRepository;
        $this->userRepository = $userRepository;

        $this->middleware('can:role.view.all')->only(['index', 'show']);
        $this->middleware('can:role.edit.all')->only(['edit', 'update']);

        $this->middleware('can:role.grant.team')->only('addUserToTeam');
        $this->middleware('can:role.edit.all')->only('removeUser');
        $this->middleware('can:profile.edit.all')->only('removeUser');
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
     * @param Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Role $role, Request $request)
    {
        $this->validate($request, [
            'displayName'   => 'required|string|max:255',
            'description'   => 'required',
            'permissions'   => 'required|array|nullable',
        ]);

        $this->roleManager->updateRole($role, $request->all());

        return redirect()->route('roles.show', ['role' => $role->getId()]);
    }

    /**
     * Add a specific user to a a specific team role.
     *
     * @param Role $role the role
     * @param User $user the user
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

        return redirect()->route('roles.show', ['role' => $role->getId()]);
    }

    /**
     * Remove a specific user from a specific role.
     *
     * @param ArrayCollection|array $list
     *
     * @return array
     */
    private function formatDotNotationList($list)
    {
        $formattedList = [];

        foreach ($list as $item) {
            list($category, $name) = explode('.', $item->getName());

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
}
