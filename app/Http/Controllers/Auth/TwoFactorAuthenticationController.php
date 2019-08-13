<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use HMS\Repositories\UserRepository;
use Illuminate\Support\Facades\Auth;
use PragmaRX\Google2FALaravel\Google2FA;

class TwoFactorAuthenticationController extends Controller
{
    /**
     * @var Google2FA
     */
    protected $google2fa;

    /**
     * @var UserRepository
     */
    protected $userRepository;

    /**
     * Create a new controller instance.
     *
     * @param Google2FA $google2fa
     *
     * @return void
     */
    public function __construct(Google2FA $google2fa, UserRepository $userRepository)
    {
        $this->google2fa = $google2fa;
        $this->userRepository = $userRepository;

        $this->middleware('auth');
    }

    /**
     * Show the 2fa enable/disable form.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function show2faForm(Request $request)
    {
        $user = Auth::user();

        $google2faUrl = '';
        if (! empty($user->getGoogle2faSecret())) {
            $google2faUrl = $this->google2fa->getQRCodeInline(
                'Nottingham Hackspace HMS',
                $user->getEmail(),
                $user->getGoogle2faSecret()
            );
        }

        return view('auth.2fa')
            ->with('user', $user)
            ->with('google2faUrl', $google2faUrl);
    }

    /**
     * Generate new 2fa secret for user.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function generate2faSecret(Request $request)
    {
        $user = Auth::user();

        // Add the secret key to the registration data
        $user->setGoogle2faEnable(false);
        $user->setGoogle2faSecret($this->google2fa->generateSecretKey(32));
        $this->userRepository->save($user);

        return redirect('2fa')->with('success', 'Secret Key is generated, Please verify Code to Enable 2FA');
    }

    /**
     * Enable 2fa for USer.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function enable2fa(Request $request)
    {
        $user = Auth::user();
        // $google2fa = app('pragmarx.google2fa');
        $secret = $request->input('verify-code');

        $valid = $this->google2fa->verifyKey($user->getGoogle2faSecret(), $secret);

        if ($valid) {
            $user->setGoogle2faEnable(true);
            $this->userRepository->save($user);

            return redirect('2fa')->with('success', '2FA is Enabled Successfully.');
        } else {
            return redirect('2fa')->with('error', 'Invalid Verification Code, Please try again.');
        }
    }

    /**
     * Disable 2fa for User.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function disable2fa(Request $request)
    {
        $validatedData = $request->validate([
            'current-password' => 'required',
        ]);

        $user = Auth::user();
        $credentials = [
            $user->getAuthIdentifierName() => $user->getAuthIdentifier(),
            'password' => $validatedData['current-password'],
        ];
        if (! Auth::attempt($credentials)) {
            return redirect()
                ->back()
                ->with('error', 'Your password does not matches with your account password. Please try again.');
        }

        $user = Auth::user();
        $user->setGoogle2faEnable(false);
        $user->setGoogle2faSecret(null);
        $this->userRepository->save($user);

        return redirect('2fa')->with('success', '2FA is now Disabled.');
    }

    /**
     * Google2FAMiddleware verify redirect.
     *
     * @return \Illuminate\Http\Response
     */
    public function verify()
    {
        return redirect(request()->session()->get('_previous')['url']);
    }
}
