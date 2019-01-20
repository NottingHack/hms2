<?php

namespace App\Http\Controllers\Auth;

use HMS\Entities\User;
use HMS\User\UserManager;
use HMS\User\ProfileManager;
use App\Http\Controllers\Controller;
use HMS\Repositories\InviteRepository;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Contracts\Validation\Factory as Validator;

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
     * @var UserManager
     */
    private $userManager;

    /**
     * @var Validator
     */
    private $validator;

    /**
     * @var ProfileManager
     */
    protected $profileManager;

    /**
     * Create a new controller instance.
     *
     * @param Validator $validator
     * @param UserManager $userManager
     */
    public function __construct(
        Validator $validator,
        UserManager $userManager,
        ProfileManager $profileManager
    ) {
        $this->middleware('guest');
        $this->validator = $validator;
        $this->userManager = $userManager;
        $this->profileManager = $profileManager;
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return $this->validator->make($data, [
            'invite' => 'required|exists:HMS\Entities\Invite,inviteToken',
            'firstname' => 'required|max:255',
            'lastname' => 'required|max:255',
            'username' => 'required|max:255|unique:HMS\Entities\User|unique:HMS\Entities\BlacklistUsername',
            'email' => 'required|email|max:255|unique:HMS\Entities\User',
            'password' => 'required|min:' . User::MIN_PASSWORD_LENGTH . '|confirmed',
            'address1' => 'required|max:100',
            'address2' => 'nullable|max:100',
            'address3' => 'nullable|max:100',
            'addressCity' => 'required|max:100',
            'addressCounty' => 'required|max:100',
            'addressPostcode' => 'required|max:10',
            'contactNumber' => 'required|max:50',
            'dateOfBirth' => 'nullable|date_format:Y-m-d',
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data called via RegistersUsers trait, passes in $request->all()
     * @return User
     */
    protected function create(array $data)
    {
        $user = $this->userManager->create(
            $data['firstname'],
            $data['lastname'],
            $data['username'],
            $data['email'],
            $data['password']
        );

        $user = $this->profileManager->create(
            $user,
            $data['address1'],
            $data['address2'],
            $data['address3'],
            $data['addressCity'],
            $data['addressCounty'],
            $data['addressPostcode'],
            $data['contactNumber'],
            $data['dateOfBirth']
        );

        return $user;
    }

    /**
     * Show the application registration form.
     * Overridden, we need to have a valid invite token.
     *
     * @return \Illuminate\Http\Response
     */
    public function showRegistrationForm(InviteRepository $inviteRepository, $token)
    {
        $invite = $inviteRepository->findOneByInviteToken($token);

        if (is_null($invite)) {
            flash('Token not found. Please visit the space to register you interest in becoming a member.', 'warning');

            return redirect('/');
        }

        return view('auth.register', [
            'invite' => $invite->getInviteToken(),
            'email' => $invite->getEmail(),
            ]);
    }
}
