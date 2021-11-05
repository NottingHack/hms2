<?php

namespace HMS\Entities;

abstract class ContentBlockType
{
    /*
     * Blade view
     */
    public const PAGE = 'PAGE';

    /*
     * Markdown view
     */
    public const EMAIL = 'EMAIL';

    /**
     * String representation of types for display.
     */
    public const TYPE_STRINGS = [
        self::PAGE => 'Page',
        self::EMAIL => 'Email',
    ];
}
