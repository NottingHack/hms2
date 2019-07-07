<?php

namespace HMS\Entities\Tools;

use Doctrine\Common\Collections\ArrayCollection;

class Tool
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
     * ToolState :: IN_USE, FREE or DISABLED.
     *
     * @var string
     */
    protected $status;

    /**
     * ToolRestriction :: UNRESTRICTED or RESTRICTED.
     *
     * @var string
     */
    protected $restrictions;

    /**
     * If tool_status=DISABLED, holds the reason why (free text).
     *
     * @var null|string
     */
    protected $statusText;

    /**
     * Cost - pence per hour.
     *
     * @var int
     */
    protected $pph;

    /**
     * Default booking length for this tool, minutes.
     *
     * @var int
     */
    protected $bookingLength;

    /**
     * Maximum amount of time a booking can be made for, minutes.
     *
     * @var int
     */
    protected $lengthMax;

    /**
     * Maximum number of booking.
     *
     * @var int
     */
    protected $bookingsMax;

    /**
     * @var Booking[]
     */
    protected $bookings;

    /**
     * Tool constructor.
     */
    public function __construct()
    {
        $this->bookings = new ArrayCollection();
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
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Gets the value of state.
     *
     * @return string
     */
    public function getStatusString()
    {
        return ToolState::STATE_STRINGS[$this->status];
    }

    /**
     * @param string $status
     *
     * @return self
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @return string
     */
    public function getRestrictions()
    {
        return $this->restrictions;
    }

    /**
     * Is usage of this tool restricted?
     *
     * @return bool true if requires an induction
     */
    public function isRestricted()
    {
        return $this->restrictions == ToolRestriction::RESTRICTED;
    }

    /**
     * @return self
     */
    public function setRestricted()
    {
        $this->restrictions = ToolRestriction::RESTRICTED;

        return $this;
    }

    /**
     * @return self
     */
    public function setUnRestricted()
    {
        $this->restrictions = ToolRestriction::UNRESTRICTED;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getStatusText()
    {
        return $this->statusText;
    }

    /**
     * @param null|string $statusText
     *
     * @return self
     */
    public function setStatusText($statusText)
    {
        $this->statusText = $statusText;

        return $this;
    }

    /**
     * @return int
     */
    public function getPph()
    {
        return $this->pph;
    }

    /**
     * @param int $pph
     *
     * @return self
     */
    public function setPph($pph)
    {
        $this->pph = $pph;

        return $this;
    }

    /**
     * @return int
     */
    public function getBookingLength()
    {
        return $this->bookingLength;
    }

    /**
     * @param int $bookingLength
     *
     * @return self
     */
    public function setBookingLength($bookingLength)
    {
        $this->bookingLength = $bookingLength;

        return $this;
    }

    /**
     * @return int
     */
    public function getLengthMax()
    {
        return $this->lengthMax;
    }

    /**
     * @param int $lengthMax
     *
     * @return self
     */
    public function setLengthMax($lengthMax)
    {
        $this->lengthMax = $lengthMax;

        return $this;
    }

    /**
     * @return int
     */
    public function getBookingsMax()
    {
        return $this->bookingsMax;
    }

    /**
     * @param int $bookingsMax
     *
     * @return self
     */
    public function setBookingsMax($bookingsMax)
    {
        $this->bookingsMax = $bookingsMax;

        return $this;
    }

    /**
     * Get name for use in permission checks.
     *
     * @return string
     */
    public function getPermissionName()
    {
        return camel_case($this->name);
    }

    /**
     * @return ArrayCollection|Booking[]
     */
    public function getBookings()
    {
        return $this->bookings;
    }
}
