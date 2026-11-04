<?php

namespace HMS\Entities\Forms;

class Form
{
    /**
     * @var int
     */
    protected $id;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $jsonDefinition;

    /*
     * @var int
     */
    protected $maxResponses;

    /*
     * @var null|string
     */
    protected $notificationKey;

    /*
     * @var string
     */
    protected $permissionName;

    /**
     * Create a new form.
     */
    public function __construct()
    {
    }

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
     * Gets the name of the form.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Sets the form name.
     *
     * @param string $name
     *
     * @return object
     */
    public function setName($name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Gets the survey.js JSON definition.
     *
     * @return string
     */
    public function getJsonDefinition()
    {
        return $this->jsonDefinition;
    }

    /**
     * Sets the survey.js JSON definition.
     *
     * @param string $jsonDefinition
     *
     * @return self
     */
    public function setJsonDefinition($jsonDefinition): self
    {
        $this->jsonDefinition = $jsonDefinition;

        return $this;
    }

    /**
     * Get the max number of responses per member.
     *
     * @return int
     */
    public function getMaxResponses()
    {
        return $this->maxResponses;
    }

    /**
     * Sets the max number of responses per member (0 is unlimited).
     *
     * @param int $maxResponses
     *
     * @return self
     */
    public function setMaxResponses($maxResponses): self
    {
        $this->maxResponses = $maxResponses;

        return $this;
    }

    /**
     * Gets the form response key used for notification routing.
     *
     * @return null|string
     */
    public function getNotificationKey()
    {
        return $this->notificationKey;
    }

    /**
     * Sets the form response key used for notification routing.
     *
     * @param string $notificationKey
     *
     * @return self
     */
    public function setNotificationkey($notificationKey): self
    {
        $this->notificationKey = $notificationKey;

        return $this;
    }

    /**
     * Gets the form permission name to delegate form edit/responses.
     *
     * @return null|string
     */
    public function getPermissionName()
    {
        return $this->permissionName;
    }

    /**
     * Sets the form permission name to delegate form edit/response.
     *
     * @param string $permissionName
     *
     * @return self
     */
    public function setPermissionName($permissionName): self
    {
        $this->permissionName = $permissionName;

        return $this;
    }
}
