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
     * @var null|null|Product
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

    /**
     * @return VendingMachine
     */
    public function getVendingMachine()
    {
        return $this->vendingMachine;
    }

    /**
     * @return string
     */
    public function getEncoding()
    {
        return $this->encoding;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return null|Product
     */
    public function getProduct()
    {
        return $this->product;
    }

    /**
     * @param null|Product $product
     *
     * @return self
     */
    public function setProduct(?Product $product)
    {
        $this->product = $product;

        return $this;
    }
}
