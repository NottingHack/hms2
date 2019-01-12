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
     * @var Product
     */
    protected $product;

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
