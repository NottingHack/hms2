<?php

namespace App\Http\Controllers\Auth;

use HMS\Accounts\AccountManager;
use HMS\Entities\User;
use Illuminate\Contracts\Validation\Factory as Validator;
use App\Http\Controllers\Controller;
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
     * @var AccountManager
     */
    private $accountManager;
    /**
     * @var Validator
     */
    private $validator;

    /**
     * Create a new controller instance.
     *
     * @param Validator $validator
     * @param AccountManager $accountManager
     */
    public function __construct(Validator $validator,
        AccountManager $accountManager)
    {
        $this->middleware('guest');
        $this->validator = $validator;
        $this->accountManager = $accountManager;
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
        return $this->accountManager->create(
            $data['name'],
            $data['username'],
            $data['email'],
            $data['password']
        );
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
