<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Mail\InterestRegistered;
use HMS\Repositories\InviteRepository;

class RegisterInterestController extends Controller
{
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
     * @param  Request          $request
     * @param  InviteRepository $inviteRepository
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

        // fire off email
        \Mail::to($request->email)
            ->queue(new InterestRegistered($invite));

        return redirect('registerInterest')->with('status', 'Thank you for Registering your interest. Please check your email.');
    }
}
