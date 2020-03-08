<?php

namespace App\Http\Controllers\Tools;

use HMS\Tools\ToolManager;
use HMS\Entities\Tools\Tool;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;
use HMS\Repositories\RoleRepository;
use HMS\Repositories\UserRepository;
use HMS\Repositories\RoleUpdateRepository;
use HMS\Repositories\Tools\ToolRepository;
use HMS\Repositories\Tools\BookingRepository;
use Illuminate\Auth\Access\AuthorizationException;

class ToolController extends Controller
{
    /**
     * @var ToolRepository
     */
    protected $toolRepository;

    /**
     * @var ToolManager
     */
    protected $toolManager;

    /**
     * @var BookingRepository
     */
    protected $bookingRepository;

    /**
     * @var UserRepository
     */
    protected $userRepository;

    /**
     * @var RoleRepository
     */
    protected $roleRepository;

    /**
     * @var RoleUpdateRepository
     */
    protected $roleUpdateRepository;

    /**
     * Create a new controller instance.
     *
     * @param ToolRepository    $toolRepository
     * @param ToolManager       $toolManager
     * @param BookingRepository $bookingRepository
     * @param UserRepository $userRepository
     * @param RoleRepository $roleRepository
     * @param RoleUpdateRepository $roleUpdateRepository
     */
    public function __construct(
        ToolRepository $toolRepository,
        ToolManager $toolManager,
        BookingRepository $bookingRepository,
        UserRepository $userRepository,
        RoleRepository $roleRepository,
        RoleUpdateRepository $roleUpdateRepository
    ) {
        $this->toolRepository = $toolRepository;
        $this->toolManager = $toolManager;
        $this->bookingRepository = $bookingRepository;
        $this->userRepository = $userRepository;
        $this->roleRepository = $roleRepository;
        $this->roleUpdateRepository = $roleUpdateRepository;

        $this->middleware('can:tools.view')->only(['index', 'show']);
        $this->middleware('can:tools.create')->only(['create', 'store']);
        $this->middleware('can:tools.edit')->only(['edit', 'update']);
        $this->middleware('can:tools.destroy')->only(['destroy']);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $tools = $this->toolRepository->findAll();
        $nextBookings = [];
        foreach ($tools as $tool) {
            $nextBookings[$tool->getId()] = $this->bookingRepository->nextForTool($tool);
        }

        return view('tools.tool.index')
            ->with('tools', $tools)
            ->with('nextBookings', $nextBookings);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('tools.tool.create');
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
        $this->validate($request, [
            'toolName'      => 'required|string|max:20|unique:HMS\Entities\Tools\Tool,name',
            'displayName'   => 'required|string|max:100',
            'restricted'    => 'sometimes|required',
            'cost'          => 'required|integer|min:0',
            'bookingLength' => 'required|integer|min:0',
            'lengthMax'     => 'required|integer|min:0',
            'bookingsMax'   => 'required|integer|min:1',
        ]);

        $tool = $this->toolManager->create(
            $request->toolName,
            $request->displayName,
            isset($request->restricted) ? true : false,
            $request->cost,
            $request->bookingLength,
            $request->lengthMax,
            $request->bookingsMax
        );
        flash('Tool \'' . $tool->getName() . '\' created.')->success();

        return redirect()->route('tools.show', ['tool' => $tool->getId()]);
    }

    /**
     * Display the specified resource.
     *
     * @param Tool $tool
     *
     * @return \Illuminate\Http\Response
     */
    public function show(Tool $tool)
    {
        return view('tools.tool.show')->with('tool', $tool);
    }

    /**
     * Display the specified resource.
     *
     * @param Tool $tool
     * @param srting $grantType
     *
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function showUsersForGrant(Tool $tool, string $grantType)
    {
        // complex permission checking based on the grantType
        if ($grantType == ToolManager::MAINTAINER) {
            if (\Gate::none([
                'tools.maintainer.grant',
                'tools.' . $tool->getPermissionName() . '.maintain',
            ])) {
                throw new AuthorizationException('This action is unauthorized.');
            }
        } elseif ($grantType == ToolManager::INDUCTOR) {
            if (\Gate::none([
                'tools.' . $tool->getPermissionName() . '.maintain',
                'tools.inductor.grant',
                'tools.' . $tool->getPermissionName() . '.induct',
            ])) {
                throw new AuthorizationException('This action is unauthorized.');
            }
        } elseif ($grantType == ToolManager::USER) {
            \Gate::authorize('tools.user.grant');
        } else {
            // should never get here
            throw new AuthorizationException('This action is unauthorized.');
        }

        $roleName = 'tools.' . $tool->getPermissionName() . '.' . strtolower($grantType);
        $role = $this->roleRepository->findOneByName($roleName);
        $users = $this->userRepository->paginateUsersWithRole($role);

        $toolUsers = [];
        // find the RoleUpdate for when the user was granted this persmision
        foreach ($users as $user) {
            $roleUpdate = $this->roleUpdateRepository->findLatestRoleAddedByUser($role, $user);
            $toolUsers[] = [
                $user,
                $roleUpdate,
            ];
        }

        return view('tools.tool.show_users')
            ->with('tool', $tool)
            ->with('grantType', $grantType)
            ->with('role', $role)
            ->with('users', $users)
            ->with('toolUsers', $toolUsers);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Tool $tool
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(Tool $tool)
    {
        return view('tools.tool.edit')->with('tool', $tool);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param Tool $tool
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Tool $tool)
    {
        $this->validate($request, [
            'toolName'      => 'required|string|max:20|unique:HMS\Entities\Tools\Tool,name,' . $tool->getId(),
            'displayName'   => 'required|string|max:100',
            'restricted'    => 'sometimes|required',
            'cost'          => 'required|integer|min:0',
            'bookingLength' => 'required|integer|min:0',
            'lengthMax'     => 'required|integer|min:0',
            'bookingsMax'    => 'required|integer|min:1',
        ]);

        $this->toolManager->update($tool, $request->all());
        flash('Tool \'' . $tool->getName() . '\' updated.')->success();

        return redirect()->route('tools.show', ['tool' => $tool->getId()]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Tool $tool
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(Tool $tool)
    {
        $this->toolManager->removeTool($tool);
        flash('Tool \'' . $tool->getName() . '\' removed.')->success();

        return redirect()->route('tools.index');
    }

    /**
     * Grant access to a Tool.
     *
     * @param Request $request
     * @param Tool $tool
     *
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function grant(Request $request, Tool $tool)
    {
        $validatedData = $request->validate([
            'grantType' => [
                'required',
                Rule::in(array_keys(ToolManager::GRANT_STRINGS)),
            ],
            'user_id' => 'required|exists:HMS\Entities\User,id',
        ]);

        // complex permission checking based on the grantType
        if ($validatedData['grantType'] == ToolManager::MAINTAINER) {
            \Gate::authorize('tools.maintainer.grant');
        } elseif ($validatedData['grantType'] == ToolManager::INDUCTOR) {
            if (\Gate::none([
                'tools.inductor.grant',
                'tools.' . $tool->getPermissionName() . '.inductor.grant',
            ])) {
                throw new AuthorizationException('This action is unauthorized.');
            }
        } elseif ($validatedData['grantType'] == ToolManager::USER) {
            \Gate::authorize('tools.user.grant');
        } else {
            // should never get here
            throw new AuthorizationException('This action is unauthorized.');
        }

        $user = $this->userRepository->findOneById($validatedData['user_id']);
        $message = $this->toolManager->grant($tool, $validatedData['grantType'], $user);

        flash($message);

        return redirect()->back();
    }
}
