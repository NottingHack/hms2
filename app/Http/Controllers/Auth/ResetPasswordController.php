<?php

namespace App\Http\Controllers\Auth;

use HMS\Entities\User;
use HMS\Auth\PasswordStore;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Password;
use Illuminate\Foundation\Auth\ResetsPasswords;

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

    /** @var PasswordStore */
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
     * @param  \Illuminate\Http\Request  $request
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

    protected function resetPassword($user, $password)
    {
        $this->passwordStore->setPassword($user->getAuthIdentifier(), $password);
        // TODO: reset the user's remember token here to ensure someone with an old cookie isn't automatically logged in

        $this->guard()->login($user);
    }
}
