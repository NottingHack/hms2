<?php

namespace App\Http\Controllers\Auth;

use HMS\Entities\User;
use HMS\Auth\PasswordStore;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Events\Users\UserPasswordChanged;

class ChangePasswordController extends Controller
{
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
        $this->passwordStore = $passwordStore;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function edit()
    {
        return view('user.changePassword')->with('user', \Auth::user());
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $user = \Auth::user();

        $valideCurrentPassword = \Auth::guard()->validate([
            $user->getAuthIdentifierName() => $user->getAuthIdentifier(),
            'password' => $request->currentPassword,
        ]);

        if (! $valideCurrentPassword) {
            flash('Your current password does not matches with the password you provided. Please try again.')->error();

            return redirect()->back();
        }

        if (strcmp($request->get('currentPassword'), $request->get('password')) == 0) {
            //Current password and new password are same
            flash('New Password cannot be same as your current password. Please choose a different password.')->error();

            return redirect()->back();
        }

        $this->validate($request, [
            'currentPassword' => 'required',
            'password' => 'required|min:' . User::MIN_PASSWORD_LENGTH . '|confirmed',
        ]);

        $this->passwordStore->setPassword($user->getAuthIdentifier(), $request->password);

        flash('Your password has been updated.')->success();

        event(new UserPasswordChanged($user));

        return redirect()->route('home');
    }
}
