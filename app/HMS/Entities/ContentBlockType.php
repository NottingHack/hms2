<?php

namespace HMS\Entities;

abstract class ContentBlockType
{
    /*
     * Blade view
     */
    const PAGE = 'PAGE';

    /*
     * Markdown view
     */
    const EMAIL = 'EMAIL';

    /**
     * String representation of types for display.
     */
    const TYPE_STRINGS = [
        self::PAGE => 'Page',
        self::EMAIL => 'Email',
    ];
}
