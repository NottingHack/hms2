<?php

namespace HMS\Entities\Snackspace;

class VendingLocation
{
    /**
     * @var int
     */
    protected $id;

    /**
     * @var VendingMachine
     */
    protected $vendingMachine;

    /**
     * @var string
     */
    protected $encoding;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var Products[]
     */
    protected $products;

    /**
     * Gets the value of id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }
}
