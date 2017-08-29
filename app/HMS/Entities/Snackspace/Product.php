<?php

namespace HMS\Entities\Snackspace;

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
     * @var string
     */
    protected $longDescription;

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
     * @return string
     */
    public function getLongDescription()
    {
        return $this->longDescription;
    }

    /**
     * @param string $longDescription
     *
     * @return self
     */
    public function setLongDescription($longDescription)
    {
        $this->longDescription = $longDescription;

        return $this;
    }
}
