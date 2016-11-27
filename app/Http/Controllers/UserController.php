<?php

namespace App\Http\Controllers;

use HMS\User\UserManager;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    private $userManager;

    /**
     * Create a new controller instance.
     */
    public function __construct(UserManager $userManager)
    {
        $this->userManager = $userManager;
    }

    /**
     * Show a specific user.
     *
     * @param int $id ID of the user
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $user = Auth::user();
        if ( ! $user->hasPermissionTo('profile.view.all')) {
            return redirect()->route('home');
        }

        $user = $this->userManager->getFormattedUser($id);

        return view('user.show')->with('user', $user);
    }
}
