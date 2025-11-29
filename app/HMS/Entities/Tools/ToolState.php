<?php

namespace HMS\Entities\Tools;

abstract class ToolState
{
    /**
     * Tool is in use.
     */
    public const IN_USE = 'IN_USE';

    /**
     * Tool is free to use.
     */
    public const FREE = 'FREE';

    /**
     * Tool has been disabled for maintenance.
     */
    public const DISABLED = 'DISABLED';

    /**
     * String representation of states for display.
     */
    public const STATE_STRINGS = [
        self::IN_USE => 'In Use',
        self::FREE => 'Free',
        self::DISABLED => 'Disabled',
    ];
}
