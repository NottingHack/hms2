<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use HMS\User\Permissions\RoleManager;

class RoleController extends Controller
{

    private $roleManager;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(RoleManager $roleManager)
    {
        $this->middleware('auth');

        $this->roleManager = $roleManager;
    }

    /**
     * Show the roles.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $roles = $this->roleManager->getFormattedRoleList();

        return view('role.index')->with('roles', $roles);
    }
}
