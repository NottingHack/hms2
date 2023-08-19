<?php

namespace App\Http\Controllers\Auth;

use App\Events\User\AccountDeletion;
use App\Http\Controllers\Controller;
use HMS\Auth\PasswordStore;
use HMS\Entities\User;
use HMS\Repositories\UserRepository;
use HMS\Repositories\ProfileRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class DeleteAccountController extends Controller
{
    /**
     * @var PasswordStore
     */
    protected $passwordStore;

    /**
     * @var UserRepository
     */
    protected $userRepository;

    /**
     * @var ProfileRepository
     */
    protected $profileRepository;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(
        PasswordStore $passwordStore,
        UserRepository $userRepository,
        ProfileRepository $profileRepository
    ) {
        $this->passwordStore = $passwordStore;
        $this->userRepository = $userRepository;
        $this->profileRepository = $profileRepository;
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

        // BankTransactions will be handled by audit job. These will
        // be obfuscated if they are older than seven years and the
        // account has been removed. Same for VendLog.
        // Unimportant information from Profile will be removed.
        // Email address is removed from User.

        $profile = $user->getProfile();

        $user->setDeletedAt(Carbon::now())
             ->obfuscate();

        $profile->obfuscate();

        $this->userRepository->save($user);
        $this->profileRepository->save($profile);

//        event(new AccountDeletion($user));

        return redirect()->route('home');
    }


}
