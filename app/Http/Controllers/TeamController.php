<?php

namespace App\Http\Controllers;

use HMS\Entities\Role;
use HMS\Repositories\RoleRepository;
use HMS\Repositories\UserRepository;
use HMS\User\Permissions\RoleManager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class TeamController extends Controller
{
    /**
     * @var RoleManager
     */
    private $roleManager;

    /**
     * @var RoleRepository
     */
    protected $roleRepository;

    /**
     * @var UserRepository
     */
    protected $userRepository;

    /**
     * Create a new controller instance.
     *
     * @param RoleManager $roleManager
     * @param RoleRepository $roleRepository
     * @param UserRepository $userRepository
     */
    public function __construct(
        RoleManager $roleManager,
        RoleRepository $roleRepository,
        UserRepository $userRepository
    ) {
        $this->roleManager = $roleManager;
        $this->roleRepository = $roleRepository;
        $this->userRepository = $userRepository;

        $this->middleware('can:team.view')->only(['index', 'show', 'howToJoin']);
        $this->middleware('can:team.create')->only(['create', 'store']);
        $this->middleware('canAny:team.edit.description,role.edit.all')->only(['edit', 'update']);
    }

    /**
     * Display a listing of all the teams.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $teams = $this->roleRepository->findAllTeams();

        return view('team.index')->with('teams', $teams);
    }

    /**
     * Display the specified team.
     *
     * @param Role $team
     *
     * @return \Illuminate\Http\Response
     */
    public function show(Role $team)
    {
        $users = $this->userRepository->paginateUsersWithRole($team);

        return view('team.show')->with('team', $team)->with('users', $users);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('team.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name'          => 'required|string',
            'displayName'   => 'required|string',
            'description'   => 'required|string',
            'email'         => 'nullable|string',
            'slackChannel'  => 'nullable|string',
        ]);

        $this->roleManager->createTeam(
            'team.' . $validatedData['name'],
            $validatedData['displayName'],
            $validatedData['description'],
            $validatedData['email'] . config('branding.email_domain'),
            '#' . $validatedData['slackChannel']
        );

        return redirect()->route('teams.index');
    }

    /**
     * Show the edit description form for a team.
     *
     * @param Role $team the Team Role
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(Role $team)
    {
        if (! (Auth::user()->hasRole($team) || Gate::allows('role.edit.all'))) {
            flash('Unauthorized')->error();

            return redirect()->route('home');
        }

        return view('team.edit')->with('team', $team);
    }

    /**
     * Update a specific team description.
     *
     * @param Role $team the Team Role
     * @param Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Role $team, Request $request)
    {
        if (! (Auth::user()->hasRole($team) || Gate::allows('role.edit.all'))) {
            flash('Unauthorized')->error();

            return redirect()->route('home');
        }

        $validatedData = $request->validate([
            'description'   => 'required',
        ]);

        $this->roleManager->updateRole($team, $validatedData);

        return redirect()->route('teams.show', ['team' => $team->getId()]);
    }

    /**
     * Display the how to join a team page.
     *
     * @return \Illuminate\Http\Response
     */
    public function howToJoin()
    {
        return view('team.how_to_join');
    }
}
