<?php

namespace App\Http\Controllers;

use App\Notifications\Users\NotifyTrusteesProfileUpdated;
use HMS\Entities\Role;
use HMS\Entities\User;
use HMS\Repositories\RoleRepository;
use HMS\Repositories\UserRepository;
use HMS\User\ProfileManager;
use HMS\User\UserManager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

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
     * @var RoleRepository
     */
    protected $roleRepository;

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
        ProfileManager $profileManager,
        RoleRepository $roleRepository
    ) {
        $this->userRepository = $userRepository;
        $this->userManager = $userManager;
        $this->profileManager = $profileManager;
        $this->roleRepository = $roleRepository;

        $this->middleware('can:profile.view.all')->only(['index', 'listUsersByRole']);
        $this->middleware('can:profile.view.self')->only(['show']);
        $this->middleware('can:profile.edit.self')->only(['edit', 'update']);
        $this->middleware('can:profile.edit.all')->only(['editAdmin']);
        $this->middleware('canAny:profile.edit.limited,profile.edit.all')->only(['editEmail', 'updateEmail']);
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
        if ($user != Auth::user()) {
            return redirect()->route('home');
        }

        return view('user.show')->with('user', $user);
    }

    /**
     * Redirects to edit($user) based on logged in user.
     *
     * @return \Illuminate\Http\Response
     */
    public function editRedirect()
    {
        if (Auth::user()) {
            return redirect()->route('users.edit', Auth::user()->getId());
        }

        return redirect()->route('login');
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
        if ($user != Auth::user() && Gate::denies('profile.edit.all')) {
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
        if ($user != Auth::user()) {
            return redirect()->route('home');
        }

        return view('user.edit')->with('user', $user);
    }

    /**
     *
     */
    private function checkChanges(User $user, $validatedData)
    {
        $profile = $user->getProfile();
        $notify = [];

        if ($profile->getAddress1() != $validatedData['address1'])
            $notify[] = "Address 1";

        if (isset($validatedData['address2']) && $profile->getAddress2() != $validatedData['address2'])
            $notify[] = "Address 2";

        if (isset($validatedData['address3']) &&  $profile->getAddress3() != $validatedData['address3'])
            $notify[] = "Address 3";

        if ($profile->getAddressCity() != $validatedData['addressCity'])
            $notify[] = "Adress City";

        if ($profile->getAddressPostcode() != $validatedData['addressPostcode'])
            $notify[] = "Address Post Code";

        if ($user->getFirstname() != $validatedData['firstname'])
            $notify[] = "First Name";

        if ($user->getLastname() != $validatedData['lastname'])
            $notify[] = "Last Name";

        if ($notify) {
            $trusteesTeamRole = $this->roleRepository->findOneByName(Role::TEAM_TRUSTEES);
            $trusteesTeamRole->notify(new NotifyTrusteesProfileUpdated($user, $notify));
        }
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
        if ($user != Auth::user() && Gate::denies('profile.edit.all')) {
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
            'discordUsername' => [
                'sometimes',
                'nullable',
                'max:36',
                'regex:/^.{2,32}#[0-9]{4}$|^[a-zA-Z0-9_\.]{2,32}$/',
            ],
            'unlockText' => 'sometimes|nullable|max:95',
        ]);

        $this->checkChanges($user, $validatedData);

        // Note:
        // Discord username validation - the username + discriminator
        // rule can be removed at some point, once Discord have
        // finished migrating old usernames to the new style.
        // https://support-dev.discord.com/hc/en-us/articles/13667755828631

        $user = $this->userManager->updateFromRequest($user, $validatedData);
        $user = $this->profileManager->updateUserProfileFromRequest($user, $validatedData);

        if ($user != Auth::user()) {
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
        if ($user != Auth::user() && Gate::denies('profile.edit.limited') && Gate::denies('profile.edit.all')) {
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
        if ($user != Auth::user() && Gate::denies('profile.edit.limited') && Gate::denies('profile.edit.all')) {
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
