<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use HMS\Auth\PasswordStore;
use HMS\Entities\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

class ResetPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset requests
    | and uses a simple trait to include this behavior. You're free to
    | explore this trait and override any methods you wish to tweak.
    |
    */

    use ResetsPasswords;

    /**
     * Where to redirect users after resetting their password.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * @var PasswordStore
     */
    protected $passwordStore;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(PasswordStore $passwordStore)
    {
        $this->middleware('guest');
        $this->passwordStore = $passwordStore;
    }

    /**
     * Reset the given user's password.
     * Note: this is overridden from the ResetPasswords trait as no mechanism is provided to customise the validation
     * rules, see: https://github.com/laravel/framework/issues/15086.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function reset(Request $request)
    {
        $this->validate($request, [
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|confirmed|min:' . User::MIN_PASSWORD_LENGTH,
        ]);

        // Here we will attempt to reset the user's password. If it is successful we
        // will update the password on an actual user model and persist it to the
        // database. Otherwise we will parse the error and return the response.
        $response = $this->broker()->reset(
            $this->credentials($request),
            function ($user, $password) {
                $this->resetPassword($user, $password);
            }
        );

        // If the password was successfully reset, we will redirect the user back to
        // the application's home authenticated view. If there is an error we can
        // redirect them back to where they came from with their error message.
        return $response == Password::PASSWORD_RESET
            ? $this->sendResetResponse($request, $response)
            : $this->sendResetFailedResponse($request, $response);
    }

    /**
     * Reset the given user's password.
     *
     * @param \Illuminate\Contracts\Auth\CanResetPassword  $user
     * @param string  $password
     *
     * @return void
     */
    protected function resetPassword($user, $password)
    {
        $this->setUserPassword($user, $password);

        $user->setRememberToken(Str::random(60));

        // @phpstan-ignore argument.type
        event(new PasswordReset($user));

        // @phpstan-ignore argument.type
        $this->guard()->login($user);
    }

    /**
     * Set the user's password.
     *
     * @param \Illuminate\Contracts\Auth\CanResetPassword  $user
     * @param string  $password
     *
     * @return void
     */
    protected function setUserPassword($user, $password)
    {
        $this->passwordStore->setPassword($user->getUsername(), $password);
    }
}
