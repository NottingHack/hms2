<?php

namespace HMS\Factories\Tools;

use HMS\Entities\Tools\Tool;
use HMS\Entities\Tools\ToolState;

class ToolFactory
{
    /**
     * Function to instantiate a new Tool from given params.
     *
     * @param string $name          Tool name (as used in mqtt topic and permisions)
     * @param string $displayName   Public title of the tool
     * @param bool   $restricted    Does this tool require an induction
     * @param int    $pph           Cost per hour in pence
     * @param int    $bookingLength Default booking length for this tool, minutes
     * @param int    $lengthMax     Maximum amount of time a booking can be made for, minutes
     * @param int    $bookingsMax   Maximum number of bookings a user can have at any one time
     * @param bool   $hidden        Whether the tool is visible to regular users
     *
     * @return Tool
     */
    public function create(
        string $name,
        string $displayName,
        bool $restricted,
        int $pph,
        int $bookingLength,
        int $lengthMax,
        int $bookingsMax = 1,
        bool $hidden = false
    ) {
        $_tool = new Tool();
        $_tool->setName($name);
        $_tool->setDisplayName($displayName);

        // set to disabled by default
        $_tool->setStatus(ToolState::DISABLED);
        $_tool->setStatusText('New tool not yet in use');

        if ($restricted) {
            $_tool->setRestricted();
        } else {
            $_tool->setUnRestricted();
        }

        $_tool->setPph($pph);
        $_tool->setBookingLength($bookingLength);
        $_tool->setLengthMax($lengthMax);
        $_tool->setBookingsMax($bookingsMax);
        $_tool->setHidden($hidden);

        return $_tool;
    }
}
