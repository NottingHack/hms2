<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use HMS\Repositories\RoleRepository;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class CanTeamEmailLoginController extends Controller
{
    public function __construct(
        protected RoleRepository $roleRepository
    ) {
        $this->middleware('feature:roundcube_login');
        $this->middleware('can:team.login-email');
    }

    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        $validatedData = $request->validate([
            'teamEmail' => [
                'required',
                'email',
                'exists:HMS\Entities\Role,email',
            ],
        ]);

        $team = $this->roleRepository->findOneByEmail($validatedData['teamEmail']);

        if (is_null($team)
            || (! (Auth::user()->hasRole($team) || Gate::allows('team.login-email.all')))
            || is_null($team->getEmailPassword())
        ) {
            return response()->json(null, Response::HTTP_UNAUTHORIZED);
        }

        return response()->json([
            'email' => $team->getEmail(),
            'emailPassword' => $team->getEmailPassword(true),
        ]);
    }
}
