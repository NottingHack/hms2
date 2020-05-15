<?php

namespace App\HMS\Entities\GateKeeper;

abstract class BuildingAccessState
{
    /**
     * The building is fully open to all members, they can come and go 24/7.
     */
    const FULL_OPEN = 10;

    /**
     * Members need to self book access to enter the building.
     */
    const SELF_BOOK = 20;

    /**
     * Members can request an access slot booking, which needs approavl before they can enter the building.
     */
    const REQUESTED_BOOK = 30;

    /**
     * The building is close to all but Trustees. They may grant acces to others.
     */
    const CLOSED = 40;

    /**
     * String representation of states for display.
     */
    const STATE_STRINGS =
    [
        self::FULL_OPEN => 'Fully open',
        self::SELF_BOOK => 'Booked access',
        self::REQUESTED_BOOK => 'Requested access',
        self::CLOSED => 'Closed',
    ];
}
