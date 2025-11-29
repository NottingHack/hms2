<?php

namespace HMS\Entities\Members;

class ProjectState
{
    /**
     * This project is considered active and being worked on.
     */
    public const ACTIVE = 10;

    /**
     * Project has been finished/removed from the hackspace.
     */
    public const COMPLETE = 20;

    /**
     * Project has been identified as abandoned and not beeing worked on.
     */
    public const ABANDONED = 30;

    /**
     * String representation of states for display.
     */
    public const STATE_STRINGS = [
        self::ACTIVE => 'Active',
        self::COMPLETE => 'Complete',
        self::ABANDONED => 'Abandoned',
    ];
}
