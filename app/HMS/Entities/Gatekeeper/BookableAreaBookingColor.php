<?php

namespace HMS\Entities\Gatekeeper;

abstract class BookableAreaBookingColor
{
    public const PRIMARY = 'primary';
    public const GREEN = 'green';
    public const RED = 'red';
    public const INDIGO = 'indigo';
    public const YELLOW = 'yellow';
    public const PINK = 'pink';
    public const ORANGE = 'orange';
    public const CYAN = 'cyan';

    /**
     * String representation of states for display.
     */
    public const COLOR_STRINGS = [
        self::PRIMARY => 'Hackspace Green',
        self::GREEN => 'Green',
        self::RED => 'Red',
        self::INDIGO => 'Indigo',
        self::YELLOW => 'Yellow',
        self::PINK => 'Pink',
        self::ORANGE => 'Orange',
        self::CYAN => 'Cyan',
    ];
}
