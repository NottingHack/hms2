<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use HMS\Repositories\RoleRepository;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class CanTeamEmailLoginController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request, RoleRepository $roleRepository)
    {
        $validatedData = $request->validate([
            'teamEmail' => [
                'required',
                'email',
                'exists:HMS\Entities\Role,email',
            ],
        ]);

        $team = $roleRepository->findOneByEmail($validatedData['teamEmail']);

        if (is_null($team)
            || ! Auth::user()->hasRole($team)
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
