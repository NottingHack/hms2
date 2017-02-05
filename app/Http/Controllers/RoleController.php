<?php

namespace App\Http\Controllers;

use HMS\User\UserManager;
use Illuminate\Http\Request;
use HMS\Repositories\RoleRepository;
use Illuminate\Support\Facades\Auth;
use HMS\User\Permissions\RoleManager;
use Illuminate\Support\Facades\Route;
use Doctrine\ORM\EntityManagerInterface;
use LaravelDoctrine\ACL\Permissions\Permission;

class RoleController extends Controller
{
    private $roleManager;
    private $roleRepository;
    private $permissionRepository;
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
    }

    /**
     * Show the roles.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = Auth::user();
        if ( ! $user->hasPermissionTo('role.view.all')) {
            return redirect()->route('home');
        }
        $roles = $this->roleRepository->findAll();

        $formattedRoles = $this->formatDotNotationList($roles);

        return view('role.index')->with('roles', $formattedRoles);
    }

    /**
     * Show a specific role.
     *
     * @param int $id ID of the role
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $user = Auth::user();
        if ( ! $user->hasPermissionTo('role.view.all')) {
            return redirect()->route('home');
        }

        $role = $this->roleRepository->find($id);

        return view('role.show')->with('role', $role);
    }

    /**
     * Show the edit form for a role.
     *
     * @param int $id ID of the role
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $user = Auth::user();
        if ( ! $user->hasPermissionTo('role.edit.all')) {
            return redirect()->route('roles.show', ['id' => $id]);
        }

        $role = $this->roleRepository->find($id);

        $permissions = $this->permissionRepository->findAll();

        $formattedPermissions = $this->formatDotNotationList($permissions);

        return view('role.edit')->with('role', $role)->with('allPermissions', $formattedPermissions);
    }

    /**
     * Update a specific role.
     *
     * @param int $id ID of the role
     * @param Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function update($id, Request $request)
    {
        $user = Auth::user();
        if ( ! $user->hasPermissionTo('role.edit.all')) {
            return redirect()->route('roles.show', ['id' => $id]);
        }

        $this->validate($request, [
            'displayName'   => 'required|string|max:255',
            'description'   => 'required',
            'permissions'   => 'required|array|nullable',
        ]);

        $this->roleManager->updateRole($id, $request->all());

        return redirect()->route('roles.show', ['id' => $id]);
    }

    /**
     * Remove a specific user from a specific role.
     *
     * @param int $roleId ID of the role
     * @param int $userId ID of the user
     * @param Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function removeUser(Request $request)
    {
        // NOTE, this is overidden by the POST data, so we need to check that our auth'd user can edit roles AND edit user
        $roleId = $request->roleId;
        $userId = $request->userId;

        $user = Auth::user();
        if ( ! $user->hasPermissionTo('role.edit.all') or ! $user->hasPermissionTo('profile.edit.all')) {
            return $this->chooseRedirect($roleId);
        }

        $this->userManager->removeRoleFromUser($userId, $this->roleRepository->find($roleId));

        return $this->chooseRedirect($roleId);
    }

    private function chooseRedirect($roleId)
    {
        if (strpos(Route::current()->getName(), 'role') !== false) {
            return redirect()->route('roles.show', ['id' => $roleId]);
        } else {
            return redirect()->route('users.show', ['id' => $userId]);
        }
    }

    public function formatDotNotationList($list) {
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
