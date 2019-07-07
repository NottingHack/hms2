<?php

namespace HMS\Entities\Tools;

abstract class UsageState
{
    const IN_PROGRESS = 'IN_PROGRESS';

    const COMPLETE = 'COMPLETE';

    const CHARGED = 'CHARGED';

    /**
     * String representation of states for display.
     */
    const STATE_STRINGS =
    [
        self::IN_PROGRESS => 'In Progress',
        self::COMPLETE => 'Complete',
        self::CHARGED => 'Carged',
    ];
}
