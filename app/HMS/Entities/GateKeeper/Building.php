<?php

namespace HMS\Entities\GateKeeper;

use Doctrine\Common\Collections\ArrayCollection;

class Building
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
}
