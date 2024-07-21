<?php

namespace Tests\Unit\HMS\Factories\Members;

use HMS\Entities\User;
use HMS\Entities\Members\ProjectState;
use HMS\Factories\Members\ProjectFactory;
use HMS\Repositories\Members\ProjectRepository;
use Illuminate\Support\Facades\Auth;
use Mockery as m;
use PHPUnit\Framework\TestCase;

class ProjectFactoryTest extends TestCase
{
    /**
     * @var User
     */
    protected $user;

    protected $projectRepository;

    public function setUp(): void
    {
        parent::setUp();

        $this->user = new User('john', 'doe', 'jdoe', 'jdoe@example.com');
        Auth::shouldReceive('user')->andReturn($this->user);

        $this->projectRepository = m::mock(ProjectRepository::class);
    }

    /**
     * @return void
     */
    public function test_create_assigns_the_project_name()
    {
        $factory = new ProjectFactory($this->projectRepository);

        $project = $factory->create('Project Name', 'Project Description');

        $this->assertEquals('Project Name', $project->getProjectName());
    }

    /**
     * @return void
     */
    public function test_create_assigns_the_project_description()
    {
        $factory = new ProjectFactory($this->projectRepository);

        $project = $factory->create('Project Name', 'Project Description');

        $this->assertEquals('Project Description', $project->getDescription());
    }

    /**
     * When first created, the new project should immediately be active.
     *
     * @return void
     */
    public function test_create_assigns_the_project_state_active() {
        $factory = new ProjectFactory($this->projectRepository);

        $project = $factory->create('Project Name', 'Project Description');

        $this->assertEquals(ProjectState::ACTIVE, $project->getState());
    }
}
