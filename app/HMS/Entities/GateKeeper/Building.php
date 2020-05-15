<?php

namespace HMS\Entities\GateKeeper;

use HMS\Traits\Entities\Timestampable;
use Doctrine\Common\Collections\ArrayCollection;

class Building
{
    use Timestampable;

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
    protected $accessState;

    /**
     * @var int
     */
    protected $selfBookMaxOccupancy;

    /**
     * @var Floor[]|ArrayCollection
     */
    protected $floors;

    public function __construct()
    {
        $this->floors = new ArrayCollection();
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
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     *
     * @return self
     */
    public function setName(string $name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return Floor[]|ArrayCollection
     */
    public function getFloors()
    {
        return $this->floors;
    }

    /**
     * @param Floor[]|ArrayCollection $floors
     *
     * @return self
     */
    public function setFloors($floors)
    {
        $this->floors = $floors;

        return $this;
    }

    /**
     * @return string
     */
    public function getAccessState()
    {
        return $this->accessState;
    }

    /**
     * Gets the value of state.
     *
     * @return string
     */
    public function getAccessStateString()
    {
        return BuildingAccessState::STATE_STRINGS[$this->accessState];
    }

    /**
     * @param string $accessState
     *
     * @return self
     */
    public function setAccessState($accessState)
    {
        $this->accessState = $accessState;

        return $this;
    }

    /**
     * @return int
     */
    public function getSelfBookMaxOccupancy()
    {
        return $this->selfBookMaxOccupancy;
    }

    /**
     * @param int $selfBookMaxOccupancy
     *
     * @return self
     */
    public function setSelfBookMaxOccupancy(int $selfBookMaxOccupancy)
    {
        $this->selfBookMaxOccupancy = $selfBookMaxOccupancy;

        return $this;
    }
}
