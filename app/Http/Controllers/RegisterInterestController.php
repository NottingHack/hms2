<?php

namespace App\Http\Controllers;

use App\Http\Requests;
use HMS\Entities\Invite;
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

    public function registerInterest(Request $request, InviteRepository $inviteRepository)
    {
        # validate the request to make sure we have a valid email and don't already have a user for that email
        $this->validate($request, [
            'email' => 'required|email|unique:HMS\Entities\User',
        ], [
            'email.unique' => 'A member with this email already exists.',
        ]);

        $invite = $inviteRepository->findOrCreateByEmail($request->email);

        # fire off email
        \Mail::to($request->email)
            ->queue(new InterestRegistered($invite));

        return redirect('registerInterest')->with('status', 'Thank you for Registering your interest. Please check your email.');
    }
}
