<?php

namespace App\Http\Controllers;

use HMS\Repositories\UserRepository;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    private $userRepository;

    /**
     * Create a new controller instance.
     */
    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;

        $this->middleware('can:profile.view.all')->only(['show']);
    }

    /**
     * Show a specific user.
     *
     * @param int $id ID of the user
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $user = $this->userRepository->find($id);

        return view('user.show')->with('user', $user);
    }
}
