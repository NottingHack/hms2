<?php

namespace HMS\Entities\GateKeeper;

abstract class BookableAreaBookingColor
{
    const PRIMARY = 'primary';
    const GREEN = 'green';
    const RED = 'red';
    const INDIGO = 'indigo';
    const YELLOW = 'yellow';
    const PINK = 'pink';
    const ORANGE = 'orange';
    const CYAN = 'cyan';

    /**
     * String representation of states for display.
     */
    const COLOR_STRINGS = [
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
