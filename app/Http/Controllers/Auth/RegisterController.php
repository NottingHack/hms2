<?php

namespace App\Http\Controllers\Auth;

use Validator;
use HMS\Entities\Role;
use HMS\Entities\User;
use HMS\Entities\Invite;
use HMS\Auth\PasswordStore;
use App\Http\Controllers\Controller;
use HMS\Repositories\RoleRepository;
use HMS\Repositories\UserRepository;
use HMS\Repositories\InviteRepository;
use Illuminate\Foundation\Auth\RegistersUsers;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after login / registration.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * @var HMS\Repositories\UserRepository
     */
    protected $userRepository;

    /**
     * @var HMS\Repositories\RoleRepository
     */
    protected $roleRepository;

    /**
     * @var HMS\Auth\PasswordStore
     */
    protected $passwordStore;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(UserRepository $userRepository, RoleRepository $roleRepository, PasswordStore $passwordStore)
    {
        $this->userRepository = $userRepository;
        $this->roleRepository = $roleRepository;
        $this->passwordStore = $passwordStore;
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'invite' => 'required|exists:HMS\Entities\Invite,inviteToken',
            'name' => 'required|max:255',
            'username' => 'required|max:255|unique:HMS\Entities\User',
            'email' => 'required|email|max:255|unique:HMS\Entities\User',
            'password' => 'required|min:' . User::MIN_PASSWORD_LENGTH . '|confirmed',
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return User
     */
    protected function create(array $data)
    {
        $user = new User(
            $data['name'],
            $data['username'],
            $data['email']
        );

        $user->getRoles()->add($this->roleRepository->findByName(Role::MEMBER_CURRENT));

        // TODO: maybe consolidate these into a single call via a service?
        $this->userRepository->create($user);
        $this->passwordStore->add($user->getUsername(), $data['password']);

        return $user;
    }

    /**
     * Show the application registration form.
     * Overidden, we need to have a valid invite token
     *
     * @return \Illuminate\Http\Response
     */
    public function showRegistrationForm(InviteRepository $inviteRepository, $token)
    {
        $invite = $inviteRepository->findOneByInviteToken($token);

        if (is_null($invite)) {
            return redirect('/');
        }

        return view('auth.register', [
            'invite' => $invite->getInviteToken(),
            'email' => $invite->getEmail(),
            ]);
    }
}
