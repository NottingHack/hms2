<?php

namespace App\Http\Controllers;

use App\Events\MembershipInterestRegistered;
use HMS\Repositories\InviteRepository;
use Illuminate\Http\Request;
use Spatie\Honeypot\ProtectAgainstSpam;

class RegisterInterestController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware(ProtectAgainstSpam::class)->only(['registerInterest']);
    }

    /**
     * Show the Register interest form.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('registerInterest');
    }

    /**
     * Register a persons interest, creating an invite token and emailing them.
     *
     * @param Request $request
     * @param InviteRepository $inviteRepository
     *
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function registerInterest(Request $request, InviteRepository $inviteRepository)
    {
        // validate the request to make sure we have a valid email and don't already have a user for that email
        $this->validate($request, [
            'email' => 'required|email|unique:HMS\Entities\User',
        ], [
            'email.unique' => 'A member with this email already exists.',
        ]);

        $invite = $inviteRepository->findOrCreateByEmail($request->email);

        event(new MembershipInterestRegistered($invite));

        flash('Thank you for registering your interest. Please check your email.');

        return redirect()->route('registerInterest');
    }
}
