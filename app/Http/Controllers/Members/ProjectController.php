<?php

namespace App\Http\Controllers\Members;

use Illuminate\Http\Request;
use HMS\Entities\Members\Project;
use App\Events\Labels\ProjectPrint;
use App\Http\Controllers\Controller;
use HMS\Repositories\UserRepository;
use Doctrine\ORM\EntityNotFoundException;
use HMS\Factories\Members\ProjectFactory;
use HMS\Repositories\Members\ProjectRepository;

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
     * Create a new controller instance.
     *
     * @param ProjectRepository $projectRepository
     * @param ProjectFactory    $projectFactory
     * @param UserRepository    $userRepository
     */
    public function __construct(ProjectRepository $projectRepository,
        ProjectFactory $projectFactory,
        UserRepository $userRepository)
    {
        $this->projectRepository = $projectRepository;
        $this->projectFactory = $projectFactory;
        $this->userRepository = $userRepository;

        $this->middleware('can:project.view.self')->only(['index', 'show']);
        $this->middleware('can:project.create.self')->only(['create', 'store']);
        $this->middleware('can:project.edit.self')->only(['edit', 'update', 'markActive', 'markAbandoned', 'markComplete']);
        $this->middleware('can:project.printLabel.self')->only(['printLabel']);
    }

    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->user) {
            $user = $this->userRepository->find($request->user);
            if (is_null($user)) {
                throw EntityNotFoundException::fromClassNameAndIdentifier(User::class, ['id' => $request->user]);
            }

            if ($user != \Auth::user() && \Gate::denies('project.view.all')) {
                flash('Unauthorized')->error();

                return redirect()->route('home');
            }
        } else {
            $user = \Auth::user();
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
     * @param  \Illuminate\Http\Request  $request
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
        flash('Project \''.$project->getProjectName().'\' created.')->success();

        return redirect()->route('projects.show', ['project' => $project->getId()]);
    }

    /**
     * Display the specified resource.
     *
     * @param  Project  $project
     * @return \Illuminate\Http\Response
     */
    public function show(Project $project)
    {
        if ($project->getUser() != \Auth::user() && \Gate::denies('project.view.all')) {
            flash('Unauthorized')->error();

            return redirect()->route('home');
        }

        return view('members.project.show')
            ->with(['project' => $project]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  Project  $project
     * @return \Illuminate\Http\Response
     */
    public function edit(Project $project)
    {
        if ($project->getUser() != \Auth::user() && \Gate::denies('project.edit.all')) {
            flash('Unauthorized')->error();

            return redirect()->route('home');
        }

        return view('members.project.edit')
            ->with(['project' => $project]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  Project  $project
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Project $project)
    {
        if ($project->getUser() != \Auth::user() && \Gate::denies('project.edit.all')) {
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

        flash('Project \''.$project->getProjectName().'\' updated.')->success();

        return redirect()->route('projects.show', ['project' => $project->getId()]);
    }

    /**
     * print a Do Not Hack label for a given project.
     *
     * @param  Project $project
     * @return \Illuminate\Http\Response
     */
    public function printLabel(Project $project)
    {
        if ($project->getUser() != \Auth::user() && \Gate::denies('project.printLabel.all')) {
            flash('Unauthorized')->error();

            return redirect()->route('home');
        }

        event(new ProjectPrint($project));
        flash('Label sent to printer.')->success();

        return back();
    }

    /**
     * mark a project active.
     *
     * @param  Project $project
     * @return \Illuminate\Http\Response
     */
    public function markActive(Project $project)
    {
        $project->setStateActive();
        $this->projectRepository->save($project);
        flash('Project \''.$project->getProjectName().'\' marked active.')->success();

        return back();
    }

    /**
     * mark a project abandoned.
     *
     * @param  Project $project
     * @return \Illuminate\Http\Response
     */
    public function markAbandoned(Project $project)
    {
        $project->setStateAbandoned();
        $this->projectRepository->save($project);
        flash('Project \''.$project->getProjectName().'\' marked abandoned.')->success();

        return back();
    }

    /**
     * mark a project complete.
     *
     * @param  Project $project
     * @return \Illuminate\Http\Response
     */
    public function markComplete(Project $project)
    {
        $project->setStateComplete();
        $this->projectRepository->save($project);
        flash('Project \''.$project->getProjectName().'\' marked complete.')->success();

        return back();
    }
}
