<?php

namespace HMS\Entities\Snackspace;

use Doctrine\Common\Collections\ArrayCollection;

class Product
{
    // avaliblity
    const AVAILABLE = 1;
    const UNAVAILABLE = 0;

    /**
     * @var int
     */
    protected $id;

    /**
     * @var int
     */
    protected $price;

    /**
     * @var null|string
     */
    protected $barcode;

    /**
     * @var null|int
     */
    protected $available;

    /**
     * @var string
     */
    protected $shortDescription;

    /**
     * @var null|string
     */
    protected $longDescription;

    /**
     * @var \Doctrine\Common\Collections\Collection|VendingLoaction[]
     */
    protected $vendingLoactions;

    /**
     * A Product constructor.
     */
    public function __construct()
    {
        $this->vendingLoactions = new ArrayCollection();
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
     * @return int
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * @param int $price
     *
     * @return self
     */
    public function setPrice($price)
    {
        $this->price = $price;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getBarcode()
    {
        return $this->barcode;
    }

    /**
     * @param null|string $barcode
     *
     * @return self
     */
    public function setBarcode($barcode)
    {
        $this->barcode = $barcode;

        return $this;
    }

    /**
     * @return null|int
     */
    public function getAvailable()
    {
        return $this->available;
    }

    /**
     * @param null|int $available
     *
     * @return self
     */
    public function setAvailable($available)
    {
        $this->available = $available;

        return $this;
    }

    /**
     * @return string
     */
    public function getShortDescription()
    {
        return $this->shortDescription;
    }

    /**
     * @param string $shortDescription
     *
     * @return self
     */
    public function setShortDescription($shortDescription)
    {
        $this->shortDescription = $shortDescription;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getLongDescription()
    {
        return $this->longDescription;
    }

    /**
     * @param null|string $longDescription
     *
     * @return self
     */
    public function setLongDescription($longDescription)
    {
        $this->longDescription = $longDescription;

        return $this;
    }

    /**
     * @return \Doctrine\Common\Collections\Collection|VendingLocation[]
     */
    public function getVendingLoaction()
    {
        return $this->vendingLoactions;
    }

    /**
     * @param \Doctrine\Common\Collections\Collection|VendingLocation[] $vendingLoactions
     *
     * @return self
     */
    public function setVendingLoaction($vendingLoactions)
    {
        $this->vendingLoactions = $vendingLoactions;

        return $this;
    }
}
