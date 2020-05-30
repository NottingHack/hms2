<?php

namespace HMS\Entities\GateKeeper;

use JsonSerializable;
use HMS\Traits\Entities\Timestampable;
use Doctrine\Common\Collections\ArrayCollection;
use Illuminate\Contracts\Support\Arrayable as ArrayableContract;

class Building implements ArrayableContract, JsonSerializable
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

    /**
     * @var Zone[]|ArrayCollection
     */
    protected $zones;

    /**
     * @var BookableArea[]|ArrayCollection
     */
    protected $bookableAreas;

    public function __construct()
    {
        $this->floors = new ArrayCollection();
        $this->zoness = new ArrayCollection();
        $this->bookableAreas = new ArrayCollection();
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

    /**
     * @return Zone[]|ArrayCollection
     */
    public function getZones()
    {
        return $this->zones;
    }

    /**
     * @param Zone[]|ArrayCollection $zones
     *
     * @return self
     */
    public function setZones($zones)
    {
        $this->zones = $zones;

        return $this;
    }

    /**
     * @return BookableArea[]|ArrayCollection
     */
    public function getBookableAreas()
    {
        return $this->bookableAreas;
    }

    /**
     * @param BookableArea[]|ArrayCollection $bookableAreas
     *
     * @return self
     */
    public function setBookableAreas($bookableAreas)
    {
        $this->bookableAreas = $bookableAreas;

        return $this;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'accessState' => $this->accessState,
            'accessStateString' => $this->getAccessStateString(),
            'selfBookMaxOccupancy' => $this->selfBookMaxOccupancy,
        ];
    }

    public function jsonSerialize()
    {
        return $this->toArray();
    }
}
