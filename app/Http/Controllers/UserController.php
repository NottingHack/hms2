<?php

namespace App\Http\Controllers;

use HMS\Entities\Role;
use HMS\Entities\User;
use HMS\User\UserManager;
use HMS\User\ProfileManager;
use Illuminate\Http\Request;
use HMS\Repositories\UserRepository;

class UserController extends Controller
{
    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * @var UserManager
     */
    protected $userManager;

    /**
     * @var ProfileManager
     */
    protected $profileManager;

    /**
     * Create a new controller instance.
     *
     * @param UserRepository $userRepository
     * @param UserManager $userManager
     * @param ProfileManager $profileManager
     */
    public function __construct(
        UserRepository $userRepository,
        UserManager $userManager,
        ProfileManager $profileManager
    ) {
        $this->userRepository = $userRepository;
        $this->userManager = $userManager;
        $this->profileManager = $profileManager;

        $this->middleware('can:profile.view.all')->only(['index', 'listUsersByRole']);
        $this->middleware('can:profile.view.self')->only(['show']);
        $this->middleware('can:profile.edit.self')->only(['edit', 'update']);
        $this->middleware('can:profile.edit.all')->only(['editAdmin']);
        $this->middleware('canAny:profile.edit.limited,profile.edit.all')->only(['editEmail', 'updateEmial']);
    }

    /**
     * Display a listing of the users.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $users = $this->userRepository->paginateAll();

        return view('user.index')
            ->with(['users' => $users]);
    }

    /**
     * Show a specific user.
     *
     * @param User $user
     *
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        if ($user != \Auth::user()) {
            return redirect()->route('home');
        }

        return view('user.show')->with('user', $user);
    }

    /**
     * Show the form for editing the specified user.
     *
     * @param User $user
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(User $user)
    {
        if ($user != \Auth::user() && \Gate::denies('profile.edit.all')) {
            return redirect()->route('home');
        }

        return view('user.edit')->with('user', $user);
    }

    /**
     * Show the form for editing the specified user.
     * This route is for admins and should have the verified middleware.
     *
     * @param User $user
     *
     * @return \Illuminate\Http\Response
     */
    public function editAdmin(User $user)
    {
        if ($user != \Auth::user()) {
            return redirect()->route('home');
        }

        return view('user.edit')->with('user', $user);
    }

    /**
     * Update the specified user in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param User $user
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user)
    {
        // TODO: if viewing someone other then authed user, authed user still needs to be email verified
        if ($user != \Auth::user() && \Gate::denies('profile.edit.all')) {
            return redirect()->route('home');
        }

        $validatedData = $request->validate([
            'firstname' => 'required|max:255',
            'lastname' => 'required|max:255',
            'email' => 'required|email|max:255|unique:HMS\Entities\User,email,' . $user->getId(),
            'address1' => 'sometimes|required|max:100',
            'address2' => 'sometimes|nullable|max:100',
            'address3' => 'sometimes|nullable|max:100',
            'addressCity' => 'sometimes|required|max:100',
            'addressCounty' => 'sometimes|required|max:100',
            'addressPostcode' => 'sometimes|required|max:10',
            'contactNumber' => 'sometimes|required|max:50',
            'dateOfBirth' => 'sometimes|nullable|date_format:Y-m-d',
            'unlockText' => 'sometimes|nullable|max:95',
        ]);

        $user = $this->userManager->updateFromRequest($user, $validatedData);
        $user = $this->profileManager->updateUserProfileFromRequest($user, $validatedData);

        if ($user != \Auth::user()) {
            return redirect()->route('users.admin.show', $user->getId());
        }

        return redirect()->route('users.show', ['user' => $user->getId()]);
    }

    /**
     * Show the form for editing the specified users email.
     *
     * @param User $user
     *
     * @return \Illuminate\Http\Response
     */
    public function editEmail(User $user)
    {
        // TODO: if viewing someone other then authed user, authed user still needs to be email verified
        if ($user != \Auth::user() && \Gate::denies('profile.edit.limited') && \Gate::denies('profile.edit.all')) {
            return redirect()->route('home');
        }

        return view('user.edit_email')->with('user', $user);
    }

    /**
     * Update the specified user in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param User $user
     *
     * @return \Illuminate\Http\Response
     */
    public function updateEmail(Request $request, User $user)
    {
        // TODO: if viewing someone other then authed user, authed user still needs to be email verified
        if ($user != \Auth::user() && \Gate::denies('profile.edit.limited') && \Gate::denies('profile.edit.all')) {
            return redirect()->route('home');
        }

        $validatedData = $request->validate([
            'email' => 'required|email|max:255|unique:HMS\Entities\User,email,' . $user->getId(),
        ]);

        $user = $this->userManager->updateFromRequest($user, $validatedData);

        return redirect()->route('users.admin.show', ['user' => $user->getId()]);
    }

    /**
     * Display a listing of the users by role.
     *
     * @param Role $role
     *
     * @return \Illuminate\Http\Response
     */
    public function listUsersByRole(Role $role)
    {
        $users = $this->userRepository->paginateUsersWithRole($role);

        return view('user.index')
            ->with([
                'users' => $users,
                'role' => $role,
            ]);
    }
}
