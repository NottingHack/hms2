<?php

namespace HMS\Entities\Snackspace;

use Doctrine\Common\Collections\ArrayCollection;

class VendingMachine
{
    /**
     * @var int
     */
    protected $id;

    /**
     * @var string
     */
    protected $description;

    /**
     * @var string
     */
    protected $type;

    /**
     * @var string
     */
    protected $connection;

    /**
     * @var string
     */
    protected $address;

    /**
     * @var \Doctrine\Common\Collections\Collection|VendingLocation[]
     */
    protected $vendingLocations;

    /**
     * Vending Machine Constructor.
     */
    public function __construct()
    {
        $this->vendingLocations = new ArrayCollection();
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
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return string
     */
    public function getTypeString()
    {
        return VendingMachineType::TYPE_STRINGS[$this->type];
    }

    /**
     * @return string
     */
    public function getConnection()
    {
        return $this->connection;
    }

    /**
     * @return string
     */
    public function getConnectionString()
    {
        return VendingMachineConnectionType::CONNECTION_STRINGS[$this->connection];
    }

    /**
     * @return string
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * @return \Doctrine\Common\Collections\Collection|VendingLocation[]
     */
    public function getVendingLocations()
    {
        return $this->vendingLocations;
    }
}
