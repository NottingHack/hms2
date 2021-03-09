<?php

namespace App\Http\Controllers\Api\Members;

use Illuminate\Http\Request;
use HMS\Entities\Members\Project;
use App\Events\Labels\ProjectPrint;
use App\Http\Controllers\Controller;
use HMS\Repositories\UserRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Doctrine\ORM\EntityNotFoundException;
use HMS\Factories\Members\ProjectFactory;
use HMS\Repositories\Members\ProjectRepository;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Response as IlluminateResponse;
use App\Http\Resources\Members\Project as ProjectResource;

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
     * @param ProjectFactory $projectFactory
     * @param UserRepository $userRepository
     */
    public function __construct(
        ProjectRepository $projectRepository,
        ProjectFactory $projectFactory,
        UserRepository $userRepository
    ) {
        $this->projectRepository = $projectRepository;
        $this->projectFactory = $projectFactory;
        $this->userRepository = $userRepository;

        $this->middleware('feature:projects');
        $this->middleware('can:project.view.self')->only(['index', 'show']);
        $this->middleware('can:project.create.self')->only(['store']);
        $this->middleware('can:project.edit.self')->only(['update', 'markActive', 'markComplete']);
        $this->middleware('can:project.edit.all')->only(['markAbandoned']);
        $this->middleware(['can:project.printLabel.self', 'feature:label_printer'])->only(['printLabel']);
    }

    /**
     * Display a listing of the resource.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     * @throws \Doctrine\ORM\EntityNotFoundException
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index(Request $request)
    {
        if ($request->user) {
            $user = $this->userRepository->findOneById($request->user);
            if (is_null($user)) {
                throw EntityNotFoundException::fromClassNameAndIdentifier(User::class, ['id' => $request->user]);
            }

            if ($user != Auth::user() && Gate::denies('project.view.all')) {
                throw new AuthorizationException('This action is unauthorized.');
            }
        } else {
            $user = Auth::user();
        }

        $projects = $this->projectRepository->findByUser($user);

        return ProjectResource::collection($projects);
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

        return (new ProjectResource($project))->response()->setStatusCode(IlluminateResponse::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     *
     * @param Project $project
     *
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function show(Project $project)
    {
        if ($project->getUser() != Auth::user() && Gate::denies('project.view.all')) {
            throw new AuthorizationException('This action is unauthorized.');
        }

        return new ProjectResource($project);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param Project $project
     *
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function update(Request $request, Project $project)
    {
        if ($project->getUser() != Auth::user() && Gate::denies('project.edit.all')) {
            throw new AuthorizationException('This action is unauthorized.');
        }

        $this->validate($request, [
            'projectName' => 'required|string|max:100',
            'description' => 'required|string',
        ]);

        $project->setProjectName($request->projectName);
        $project->setDescription($request->description);
        $this->projectRepository->save($project);

        return new ProjectResource($project);
    }

    /**
     * Print a Do Not Hack label for a given project.
     *
     * @param Project $project
     *
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function printLabel(Project $project)
    {
        if ($project->getUser() != Auth::user() && Gate::denies('project.printLabel.all')) {
            throw new AuthorizationException('This action is unauthorized.');
        }

        event(new ProjectPrint($project));

        return response()->json([], IlluminateResponse::HTTP_ACCEPTED);
    }

    /**
     * Mark a project active.
     *
     * @param Project $project
     *
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function markActive(Project $project)
    {
        if ($project->getUser() != Auth::user() && Gate::denies('project.edit.all')) {
            throw new AuthorizationException('This action is unauthorized.');
        }

        $project->setStateActive();
        $this->projectRepository->save($project);

        return new ProjectResource($project);
    }

    /**
     * Mark a project abandoned.
     *
     * @param Project $project
     *
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function markAbandoned(Project $project)
    {
        if ($project->getUser() == Auth::user()) {
            throw new AuthorizationException('This action is unauthorized.');
        }

        $project->setStateAbandoned();
        $this->projectRepository->save($project);

        return new ProjectResource($project);
    }

    /**
     * Mark a project complete.
     *
     * @param Project $project
     *
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function markComplete(Project $project)
    {
        if ($project->getUser() != Auth::user()) {
            throw new AuthorizationException('This action is unauthorized.');
        }

        $project->setStateComplete();
        $this->projectRepository->save($project);

        return new ProjectResource($project);
    }
}
