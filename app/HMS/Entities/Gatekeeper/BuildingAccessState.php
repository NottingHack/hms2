<?php

namespace HMS\Entities\Gatekeeper;

abstract class BuildingAccessState
{
    /**
     * The building is fully open to all members, they can come and go 24/7.
     */
    public const FULL_OPEN = 'FULL_OPEN';

    /**
     * Members need to self book access to enter the building.
     */
    public const SELF_BOOK = 'SELF_BOOK';

    /**
     * Members can request an access slot booking, which needs approval before they can enter the building.
     */
    public const REQUESTED_BOOK = 'REQUESTED_BOOK';

    /**
     * The building is closed to all but Trustees. They may grant access to others.
     */
    public const CLOSED = 'CLOSED';

    /**
     * String representation of states for display.
     */
    public const STATE_STRINGS = [
        self::FULL_OPEN => 'Fully open',
        self::SELF_BOOK => 'Self booked access',
        self::REQUESTED_BOOK => 'Requested access',
        self::CLOSED => 'Closed',
    ];
}
