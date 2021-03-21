<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Gate;

class CanCheckController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware('can:profile.view.self');
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
            'permission' => 'required_without_all:permissions|string|exists:LaravelDoctrine\ACL\Permissions\Permission,name',
            'permissions' => 'required_without_all:permission|array',
            'permissions.*' => 'required_with:permissions|string|exists:LaravelDoctrine\ACL\Permissions\Permission,name',
        ]);

        if (array_key_exists('permission', $validatedData)) {
            $permissionsToCheck = Arr::wrap($validatedData['permission']);
        } else {
            $permissionsToCheck = $validatedData['permissions'];
        }

        $results = [];

        foreach ($permissionsToCheck as $permission) {
            $results[] = [
                $permission => Gate::allows($permission),
            ];
        }

        return response()->json($results);
    }
}
