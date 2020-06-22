<?php

namespace HMS\Entities\Gatekeeper;

use JsonSerializable;
use HMS\Traits\Entities\SoftDeletable;
use HMS\Traits\Entities\Timestampable;
use Illuminate\Contracts\Support\Arrayable as ArrayableContract;

class BookableArea implements ArrayableContract, JsonSerializable
{
    use Timestampable, SoftDeletable;

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
    protected $description;

    /**
     * @var int
     */
    protected $maxOccupancy;

    /**
     * @var int
     */
    protected $additionalGuestOccupancy;

    /**
     * @var string
     */
    protected $bookingColor;

    /**
     * @var bool
     */
    protected $selfBookable;

    /**
     * @var Building
     */
    protected $building;

    public function __construct()
    {
        $this->maxOccupancy = 1;
        $this->additionalGuestOccupancy = 0;
        $this->selfBookable = false;
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
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @return string
     */
    public function getDescriptionTrimed($length = 100)
    {
        if (strlen($this->description) > $length) {
            return substr($this->description, 0, $length) . '...';
        }

        return $this->description;
    }

    /**
     * @param string $description
     *
     * @return self
     */
    public function setDescription(string $description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return int
     */
    public function getMaxOccupancy()
    {
        return $this->maxOccupancy;
    }

    /**
     * @param int $maxOccupancy
     *
     * @return self
     */
    public function setMaxOccupancy(int $maxOccupancy)
    {
        $this->maxOccupancy = $maxOccupancy;

        return $this;
    }

    /**
     * @return int
     */
    public function getAdditionalGuestOccupancy()
    {
        return $this->additionalGuestOccupancy;
    }

    /**
     * @param int $additionalGuestOccupancy
     *
     * @return self
     */
    public function setAdditionalGuestOccupancy(int $additionalGuestOccupancy)
    {
        $this->additionalGuestOccupancy = $additionalGuestOccupancy;

        return $this;
    }

    /**
     * @return string
     */
    public function getBookingColor()
    {
        return $this->bookingColor;
    }

    /**
     * Gets the string representation of booking color.
     *
     * @return string
     */
    public function getBookingColorString()
    {
        return BookableAreaBookingColor::COLOR_STRINGS[$this->bookingColor];
    }

    /**
     * @param string $bookingColor
     *
     * @return self
     */
    public function setBookingColor(string $bookingColor)
    {
        $this->bookingColor = $bookingColor;

        return $this;
    }

    /**
     * @return bool
     */
    public function isSelfBookable()
    {
        return $this->selfBookable;
    }

    /**
     * @param bool $selfBookable
     *
     * @return self
     */
    public function setSelfBookable($selfBookable)
    {
        $this->selfBookable = $selfBookable;

        return $this;
    }

    /**
     * @return Building
     */
    public function getBuilding()
    {
        return $this->building;
    }

    /**
     * @param Building $building
     *
     * @return self
     */
    public function setBuilding(Building $building)
    {
        $this->building = $building;

        return $this;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return [
            'id' => $this->getId(),
            'name' => $this->getName(),
            'description' => $this->getDescription(),
            'maxOccupancy' => $this->getMaxOccupancy(),
            'additionalGuestOccupancy' => $this->getAdditionalGuestOccupancy(),
            'bookingColor' => $this->getBookingColor(),
            'bookingColorString'  => $this->getBookingColorString(),
            'selfBookable' => $this->isSelfBookable(),
            'building' => $this->getBuilding(),
        ];
    }

    public function jsonSerialize()
    {
        return $this->toArray();
    }
}
