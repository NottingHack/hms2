<?php

namespace HMS\Entities\Tools;

abstract class ToolState
{
    /**
     * Tool is in use.
     */
    const IN_USE = 'IN_USE';

    /**
     * Tool is free to use.
     */
    const FREE = 'FREE';

    /**
     * Tool has been disabled for maintenance.
     */
    const DISABLED = 'DISABLED';

    /**
     * String representation of states for display.
     */
    const STATE_STRINGS =
    [
        self::IN_USE => 'In Use',
        self::FREE => 'Free',
        self::DISABLED => 'Disabled',
    ];
}
