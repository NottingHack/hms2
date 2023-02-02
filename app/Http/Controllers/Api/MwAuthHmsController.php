<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class MwAuthHmsController extends Controller
{
    public function __construct()
    {
        $this->middleware('feature:mw_auth_hms');
    }

    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        $validatedData = $request->validate([
            'function' => 'required|string',
            'username' => 'required|string',
            'password' => 'required|string',
            'hash' => 'required|string',
        ]);

        $sentHash = $validatedData['hash'];
        $validatedData['hash'] = config('services.mw_auth_hms.secret');
        $ourHash = crypt(json_encode($validatedData), config('services.mw_auth_hms.salt'));
        if ($sentHash != $ourHash) {
            return response()->json(['error' => 'Decode error']);
        }

        if ($validatedData['function'] != 'login') {
            return response()->json(['error' => 'Unknown function']);
        }

        /* Replace anything that isn't a-Z, 0-9 with an underscore (mostly after spaces...) */
        $username = preg_replace('/[^a-zA-Z0-9]/', '_', $validatedData['username']);
        if (! Auth::once([
                'username' => lcfirst($username),
                'password' => $validatedData['password'],
            ])
            || ! Auth::once([
                'username' => ucfirst($username),
                'password' => $validatedData['password'],
            ])
        ) {
            return response()->json([
                'access_granted' => false,
                'error' => 'Unauthorized',
            ]);
        }

        $user = Auth::user();

        return response()->json([
            'access_granted' => true,
            'username' => $user->getUsername(),
            'name' => $user->getFullname(),
            'email' => $user->getEmail(),
        ]);
    }
}
