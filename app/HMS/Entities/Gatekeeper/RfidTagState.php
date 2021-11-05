<?php

namespace HMS\Entities\Gatekeeper;

abstract class RfidTagState
{
    /**
     * This RfidTag can be used for entry (up until the expiry date), cannot be used to register a card.
     */
    public const ACTIVE = 10;

    /**
     * RfidTag has been destroyed and can no longer be used for entry.
     */
    public const EXPIRED = 20;

    /**
     * RfidTag has been lost and can no longer be used for entry.
     */
    public const LOST = 30;

    /**
     * String representation of states for display.
     */
    public const STATE_STRINGS =
    [
        self::ACTIVE => 'Active',
        self::EXPIRED => 'Destroyed',
        self::LOST => 'Lost',
    ];
}
