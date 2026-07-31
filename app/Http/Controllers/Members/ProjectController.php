<?php

namespace App\Http\Controllers\Members;

use App\Events\Labels\ProjectPrint;
use App\Http\Controllers\Controller;
use App\Notifications\ProjectRemovalRequest;
use Carbon\Carbon;
use Doctrine\ORM\EntityNotFoundException;
use HMS\Entities\Members\Project;
use HMS\Entities\Role;
use HMS\Entities\User;
use HMS\Factories\Members\ProjectFactory;
use HMS\Repositories\Members\ProjectRepository;
use HMS\Repositories\RoleRepository;
use HMS\Repositories\UserRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class ProjectController extends Controller
{
    /**
     * @var ProjectRepository
     */
    protected $projectRepository;

    /**
     * @var ProjectFactory
     */
    protected $projectFactory;

    /**
     * @var UserRepository
     */
    protected $userRepository;

    /**
     * @var RoleRepository
     */
    protected $roleRepository;

    /**
     * Create a new controller instance.
     *
     * @param ProjectRepository $projectRepository
     * @param ProjectFactory $projectFactory
     * @param UserRepository $userRepository
     * @param RoleRepository $roleRepository
     */
    public function __construct(
        ProjectRepository $projectRepository,
        ProjectFactory $projectFactory,
        UserRepository $userRepository,
        RoleRepository $roleRepository,
    ) {
        $this->projectRepository = $projectRepository;
        $this->projectFactory = $projectFactory;
        $this->userRepository = $userRepository;
        $this->roleRepository = $roleRepository;

        $this->middleware('feature:projects');
        $this->middleware('can:project.view.self')->only(['index', 'show']);
        $this->middleware('can:project.create.self')->only(['create', 'store']);
        $this->middleware('can:project.edit.self')->only(['edit', 'update', 'markActive', 'markComplete']);
        $this->middleware('can:project.edit.all')->only(['markAbandoned']);
        $this->middleware(['can:project.printLabel.self', 'feature:label_printer'])->only(['printLabel']);
        $this->middleware('can:project.tort')->only(['tort', 'clearTort']);
    }

    /**
     * Display a listing of the resource.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->user) {
            $user = $this->userRepository->findOneById($request->user);
            if (is_null($user)) {
                throw EntityNotFoundException::fromClassNameAndIdentifier(User::class, ['id' => $request->user]);
            }

            if ($user != Auth::user() && Gate::denies('project.view.all')) {
                flash('Unauthorized')->error();

                return redirect()->route('home');
            }
        } else {
            $user = Auth::user();
        }

        $projects = $this->projectRepository->paginateByUser($user);

        return view('members.project.index')
            ->with(['user' => $user, 'projects' => $projects]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // give the view an empty project so we can reuse the form parital
        $project = $this->projectFactory->create('', '');

        return view('members.project.create')
            ->with(['project' => $project]);
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
            'projectName' => 'required|string|max:100',
            'description' => 'required|string',
        ]);

        $project = $this->projectFactory->create($request->projectName, $request->description);
        $this->projectRepository->save($project);
        flash('Project \'' . $project->getProjectName() . '\' created.')->success();

        return redirect()->route('projects.show', ['project' => $project->getId()]);
    }

    /**
     * Display the specified resource.
     *
     * @param Project $project
     *
     * @return \Illuminate\Http\Response
     */
    public function show(Project $project)
    {
        if ($project->getUser() != Auth::user() && Gate::denies('project.view.all')) {
            flash('Unauthorized')->error();

            return redirect()->route('home');
        }

        return view('members.project.show')
            ->with(['project' => $project]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Project $project
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(Project $project)
    {
        if ($project->getUser() != Auth::user() && Gate::denies('project.edit.all')) {
            flash('Unauthorized')->error();

            return redirect()->route('home');
        }

        return view('members.project.edit')
            ->with(['project' => $project]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param Project $project
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Project $project)
    {
        if ($project->getUser() != Auth::user() && Gate::denies('project.edit.all')) {
            flash('Unauthorized')->error();

            return redirect()->route('home');
        }

        $this->validate($request, [
            'projectName' => 'required|string|max:100',
            'description' => 'required|string',
        ]);

        $project->setProjectName($request->projectName);
        $project->setDescription($request->description);
        $this->projectRepository->save($project);

        flash('Project \'' . $project->getProjectName() . '\' updated.')->success();

        return redirect()->route('projects.show', ['project' => $project->getId()]);
    }

    /**
     * Print a Do Not Hack label for a given project.
     *
     * @param Project $project
     *
     * @return \Illuminate\Http\Response
     */
    public function printLabel(Project $project)
    {
        if ($project->getUser() != Auth::user() && Gate::denies('project.printLabel.all')) {
            flash('Unauthorized')->error();

            return redirect()->route('home');
        }

        event(new ProjectPrint($project));
        flash('Label sent to printer.')->success();

        return back();
    }

    /**
     * Mark a project active.
     *
     * @param Project $project
     *
     * @return \Illuminate\Http\Response
     */
    public function markActive(Project $project)
    {
        if ($project->getUser() != Auth::user() && Gate::denies('project.edit.all')) {
            flash('Unauthorized')->error();

            return redirect()->route('home');
        }

        $project->setStateActive();
        $this->projectRepository->save($project);
        flash('Project \'' . $project->getProjectName() . '\' marked active.')->success();

        return back();
    }

    /**
     * Mark a project abandoned.
     *
     * @param Project $project
     *
     * @return \Illuminate\Http\Response
     */
    public function markAbandoned(Project $project)
    {
        if ($project->getUser() == Auth::user()) {
            flash('You can not abandoned your own project')->error();

            return redirect()->route('home');
        }

        $project->setStateAbandoned();
        $this->projectRepository->save($project);
        flash('Project \'' . $project->getProjectName() . '\' marked abandoned.')->success();

        return back();
    }

    /**
     * Mark a project complete.
     *
     * @param Project $project
     *
     * @return \Illuminate\Http\Response
     */
    public function markComplete(Project $project)
    {
        if ($project->getUser() != Auth::user()) {
            flash('Unauthorized')->error();

            return redirect()->route('home');
        }

        $project->setStateComplete();
        $this->projectRepository->save($project);
        flash('Project \'' . $project->getProjectName() . '\' marked complete.')->success();

        return back();
    }

    /**
     * Tort a project (request removal).
     *
     * @param Project $project
     *
     * @return \Illuminate\Http\Response
     */
    public function tort(Project $project)
    {
        return view('members.project.tort')
            ->with(['project' => $project]);
    }

    /**
     * Perform a tort a project (request removal).
     *
     * @param Project $project
     *
     * @return \Illuminate\Http\Response
     */
    public function performTort(Request $request, Project $project)
    {
        $this->validate($request, [
            'tortReason' => 'required|string',
        ]);

        $project->setTortReason($request->tortReason);
        $project->setTortDate(Carbon::now());
        $this->projectRepository->save($project);

        $trusteesTeamRole = $this->roleRepository->findOneByName(Role::TEAM_TRUSTEES);
        $trusteesTeamRole->notify(new ProjectRemovalRequest($project));
        $project->getUser()->notify(new ProjectRemovalRequest($project));

        return redirect()->route('projects.show', ['project' => $project->getId()]);
    }

    /**
     * Clear an earlier tort request.
     *
     * @param Project $project
     *
     * @return \Illuminate\Http\Response
     */
    public function clearTort(Project $project)
    {
        $project->setTortReason(null);
        $project->setTortDate(null);
        $this->projectRepository->save($project);

        return redirect()->route('projects.show', ['project' => $project->getId()]);
    }
}
