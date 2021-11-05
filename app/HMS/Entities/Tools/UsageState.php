<?php

namespace HMS\Entities\Tools;

abstract class UsageState
{
    public const IN_PROGRESS = 'IN_PROGRESS';
    public const COMPLETE = 'COMPLETE';
    public const CHARGED = 'CHARGED';

    /**
     * String representation of states for display.
     */
    public const STATE_STRINGS =
    [
        self::IN_PROGRESS => 'In Progress',
        self::COMPLETE => 'Complete',
        self::CHARGED => 'Carged',
    ];
}
