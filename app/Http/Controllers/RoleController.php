<?php

namespace App\Http\Controllers;

use HMS\User\UserManager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use HMS\User\Permissions\RoleManager;
use Illuminate\Support\Facades\Route;
use HMS\User\Permissions\PermissionManager;


class RoleController extends Controller
{

    private $roleManager;
    private $permissionManager;
    private $userManager;

    /**
     * Create a new controller instance.
     */
    public function __construct(RoleManager $roleManager, PermissionManager $permissionManager, UserManager $userManager)
    {
        $this->roleManager = $roleManager;
        $this->permissionManager = $permissionManager;
        $this->userManager = $userManager;
    }

    /**
     * Show the roles.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = Auth::user();
        if (!$user->hasPermissionTo('role.view.all')) {
            return redirect()->route('home');
        }
        $roles = $this->roleManager->getFormattedRoleList();

        return view('role.index')->with('roles', $roles);
    }

    /**
     * Show a specific role.
     *
     * @param integer $id ID of the role
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $user = Auth::user();
        if (!$user->hasPermissionTo('role.view.all')) {
            return redirect()->route('home');
        }

        $role = $this->roleManager->getFormattedRole($id);

        return view('role.show')->with('role', $role);
    }


    /**
     * Show the edit form for a role.
     *
     * @param integer $id ID of the role
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $user = Auth::user();
        if (!$user->hasPermissionTo('role.edit.all')) {
            return redirect()->route('roles.show', [ 'id' => $id ]);
        }

        $permissions = $this->permissionManager->getFormattedPermissionList();

        $role = $this->roleManager->getFormattedRole($id);

        return view('role.edit')->with('role', $role)->with('allPermissions', $permissions);
    }

    /**
     * Update a specific role.
     *
     * @param integer $id ID of the role
     * @param Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function update($id, Request $request)
    {
        $user = Auth::user();
        if (!$user->hasPermissionTo('role.edit.all')) {
            return redirect()->route('roles.show', [ 'id' => $id ]);
        }

        $this->validate($request, [
            'displayName'   => 'required|string|max:255',
            'description'   => 'required',
            'permissions'   => 'required|array|nullable',
        ]);

        $this->roleManager->updateRole($id, $request->all());

        return redirect()->route('roles.show', [ 'id' => $id ]);
    }

    /**
     * Remove a specific user from a specific role.
     *
     * @param integer $roleId ID of the role
     * @param integer $userId ID of the user
     * @param Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function removeUser(Request $request)
    {
        // NOTE, this is overidden by the POST data, so we need to check that our auth'd user can edit roles AND edit user
        $roleId = $request->roleId;
        $userId = $request->userId;

        $user = Auth::user();
        if (!$user->hasPermissionTo('role.edit.all') or !$user->hasPermissionTo('profile.edit.all')) {
            return $this->chooseRedirect($roleId);
        }

        $this->userManager->removeRoleFromUser($userId, $this->roleManager->getRole($roleId));

        return $this->chooseRedirect($roleId);
    }

    private function chooseRedirect($roleId)
    {
        if (strpos(Route::current()->getName(), 'role') !== false) {
            return redirect()->route('roles.show', [ 'id' => $roleId ]);
        } else {
            return redirect()->route('users.show', [ 'id' => $userId ]);
        }
    }
}
