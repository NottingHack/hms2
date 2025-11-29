<?php

namespace HMS\Entities\Members;

class BoxState
{
    /**
     * This box is considered active and being used.
     */
    public const INUSE = 10;

    /**
     * Box has been removed from the hackspace.
     */
    public const REMOVED = 20;

    /**
     * Box has been identified as abandoned and not beeing worked on.
     */
    public const ABANDONED = 30;

    /**
     * String representation of states for display.
     */
    public const STATE_STRINGS = [
        self::INUSE => 'In Use',
        self::REMOVED => 'Removed',
        self::ABANDONED => 'Abandoned',
    ];
}
