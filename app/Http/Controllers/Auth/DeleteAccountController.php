<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use HMS\Auth\PasswordStore;
use HMS\Entities\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DeleteAccountController extends Controller
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
    public function info()
    {
        return view('user.deleteAccount')->with('user', Auth::user());
    }

    /**
     * Initiate the account removal process
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function delete(Request $request)
    {
        $user = Auth::user();

        $valideCurrentPassword = Auth::guard()->validate([
            'username' => $user->getUsername(),
            'password' => $request->currentPassword,
        ]);

        if (! $valideCurrentPassword) {
            flash('Your current password does not matches with the password you provided. Please try again.')->error();
            return redirect()->back();
        }

        //event(new AccountDeletion($user));

        return redirect()->route('home');
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
