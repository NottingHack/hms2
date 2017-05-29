<?php

namespace App\Http\Controllers;

use HMS\Entities\User;
use HMS\Repositories\UserRepository;

class UserController extends Controller
{
    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * Create a new controller instance.
     */
    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;

        $this->middleware('can:profile.view.self')->only(['show']);
    }

    /**
     * Show a specific user.
     *
     * @param User $user
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        if ($user != \Auth::user() && \Gate::denies('profile.view.all') ) {
            return redirect()->route('home');
        }

        return view('user.show')->with('user', $user);
    }
}
