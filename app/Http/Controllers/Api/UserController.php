<?php

namespace App\Http\Controllers\Api;

use HMS\Entities\User;
use HMS\User\UserManager;
use HMS\User\ProfileManager;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use HMS\Repositories\UserRepository;
use App\Http\Resources\User as UserResource;

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

        $this->middleware('can:profile.view.self')->only(['show']);
        $this->middleware('can:profile.edit.self')->only(['update']);
    }

    /**
     * Show a specific user.
     *
     * @param User $user
     *
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function show(User $user)
    {
        if ($user != \Auth::user()) {
            throw new AuthorizationException('This action is unauthorized.');
        }

        return new UserResource($user);
    }

    /**
     * Update the specified user in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param User $user
     *
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function update(Request $request, User $user)
    {
        // TODO: if viewing someone other then authed user, authed user still needs to be email verified
        if ($user != \Auth::user() && \Gate::denies('profile.edit.all')) {
            throw new AuthorizationException('This action is unauthorized.');
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

        return new UserResource($user);
    }
}
