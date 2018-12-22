<?php

namespace HMS\Entities\Members;

class BoxState
{
    /**
     * This box is considered active and being used.
     */
    const INUSE = 10;

    /**
     * Box has been removed from the hackspace.
     */
    const REMOVED = 20;

    /**
     * Box has been identified as abandoned and not beeing worked on.
     */
    const ABANDONED = 30;

    /**
     * String representation of states for display.
     */
    const STATE_STRINGS =
    [
        self::INUSE => 'In Use',
        self::REMOVED => 'Removed',
        self::ABANDONED => 'Abandoned',
    ];
}
