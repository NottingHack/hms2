<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use HMS\User\Permissions\RoleManager;
use HMS\User\Permissions\PermissionManager;


class RoleController extends Controller
{

    private $roleManager;
    private $permissionManager;

    /**
     * Create a new controller instance.
     */
    public function __construct(RoleManager $roleManager, PermissionManager $permissionManager)
    {
        $this->roleManager = $roleManager;
        $this->permissionManager = $permissionManager;
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
}
