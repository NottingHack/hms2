<?php

namespace HMS\Factories\Members;

use HMS\Entities\Members\Project;
use HMS\Repositories\Members\ProjectRepository;

class ProjectFactory
{
    /**
     * @var ProjectRepository
     */
    protected $projectRepository;

    /**
     * @param ProjectRepository $projectRepository
     */
    public function __construct(ProjectRepository $projectRepository)
    {
        $this->projectRepository = $projectRepository;
    }

    /**
     * Function to instantiate a new Project from given params.
     *
     * @param string $name
     * @param string $description
     */
    public function create($name, $description)
    {
        $_project = new Project();
        $_project->setProjectName($name);
        $_project->setDescription($description);
        $_project->setStateActive();
        $_project->setUser(\Auth::user());

        return $_project;
    }
}
