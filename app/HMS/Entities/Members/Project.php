<?php

namespace HMS\Entities\Members;

use Carbon\Carbon;
use HMS\Entities\User;

class Project
{
    /**
     * Length of the trimmed description.
     */
    public const TRIMMED_LENGHT = 100;

    /**
     * @var int
     */
    protected $id;

    /**
     * @var string
     */
    protected $projectName;

    /**
     * @var string
     */
    protected $description;

    /**
     * @var Carbon
     */
    protected $startDate;

    /**
     * @var null|Carbon
     */
    protected $completeDate;

    /**
     * @var int
     */
    protected $state;

    /**
     * @var User
     */
    protected $user;

    /**
     * Gets the value of id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getProjectName()
    {
        return $this->projectName;
    }

    /**
     * @param string $projectName
     *
     * @return self
     */
    public function setProjectName($projectName)
    {
        $this->projectName = $projectName;

        return $this;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @return string
     */
    public function getDescriptionTrimed()
    {
        if (strlen($this->description) > self::TRIMMED_LENGHT) {
            return substr($this->description, 0, self::TRIMMED_LENGHT) . '...';
        }

        return $this->description;
    }

    /**
     * @param string $description
     *
     * @return self
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return Carbon
     */
    public function getStartDate()
    {
        return $this->startDate;
    }

    /**
     * @param Carbon $startDate
     *
     * @return self
     */
    public function setStartDate(Carbon $startDate)
    {
        $this->startDate = $startDate;

        return $this;
    }

    /**
     * Gets the value of state.
     *
     * @return string
     */
    public function getStateString()
    {
        return ProjectState::STATE_STRINGS[$this->state];
    }

    /**
     * @return null|Carbon
     */
    public function getCompleteDate()
    {
        return $this->completeDate;
    }

    /**
     * @param null|Carbon $completeDate
     *
     * @return self
     */
    public function setCompleteDate(?Carbon $completeDate)
    {
        $this->completeDate = $completeDate;

        return $this;
    }

    /**
     * @return int
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * @param int $state
     *
     * @return self
     */
    public function setState($state)
    {
        if (array_key_exists($state, ProjectState::STATE_STRINGS)) {
            $this->state = $state;
        }

        return $this;
    }

    /**
     * @return self
     */
    public function setStateActive()
    {
        $this->state = ProjectState::ACTIVE;
        $this->completeDate = null;

        return $this;
    }

    /**
     * @return self
     */
    public function setStateComplete()
    {
        $this->state = ProjectState::COMPLETE;
        $this->completeDate = Carbon::now();

        return $this;
    }

    /**
     * @return self
     */
    public function setStateAbandoned()
    {
        $this->state = ProjectState::ABANDONED;
        $this->completeDate = Carbon::now();

        return $this;
    }

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param User $user
     *
     * @return self
     */
    public function setUser(User $user)
    {
        $this->user = $user;

        return $this;
    }
}
