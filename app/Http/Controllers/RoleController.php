<?php

namespace App\Http\Controllers;

use HMS\Entities\Role;
use HMS\Entities\User;
use HMS\User\UserManager;
use Illuminate\Http\Request;
use HMS\Repositories\RoleRepository;
use HMS\User\Permissions\RoleManager;
use Illuminate\Support\Facades\Route;
use Doctrine\ORM\EntityManagerInterface;
use LaravelDoctrine\ACL\Permissions\Permission;
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
     * Create a new controller instance.
     */
    public function __construct(RoleManager $roleManager, RoleRepository $roleRepository, UserManager $userManager, EntityManagerInterface $em)
    {
        $this->roleManager = $roleManager;
        $this->roleRepository = $roleRepository;
        $this->userManager = $userManager;
        // TODO: replace with actual repository
        $this->permissionRepository = $em->getRepository(Permission::class);

        $this->middleware('can:role.view.all')->only(['index', 'show']);
        $this->middleware('can:role.edit.all')->only(['edit', 'update']);

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
     * @return \Illuminate\Http\Response
     */
    public function show(Role $role)
    {
        return view('role.show')->with('role', $role);
    }

    /**
     * Show the edit form for a role.
     *
     * @param Role $role the Role
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
     * Remove a specific user from a specific role.
     *
     * @param int $roleId ID of the role
     * @param int $userId ID of the user
     * @param Illuminate\Http\Request $request
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
     * @param ArrayCollection $list
     * @return array
     */
    private function formatDotNotationList(ArrayCollection $list)
    {
        $formattedList = [];

        foreach ($list as $item) {
            list($category, $name) = explode('.', $item->getName());

            if ( ! isset($formattedList[$category])) {
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
