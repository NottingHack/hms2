<?php

namespace HMS\Entities\Gatekeeper;

abstract class PinState
{
    /**
     * This pin can be used for entry (up until the expiry date), cannot be used to register a card.
     */
    public const ACTIVE = 10;

    /**
     * Pin has expired and can no longer be used for entry.
     */
    public const EXPIRED = 20;

    /**
     * This pin cannot be used for entry, and has likely been used to activate an RFID card.
     */
    public const CANCELLED = 30;

    /**
     * This pin may be used to enrol an RFID card.
     */
    public const ENROLL = 40;

    /**
     * String representation of states for display.
     */
    public const STATE_STRINGS = [
        self::ACTIVE => 'Active',
        self::EXPIRED => 'Expired',
        self::CANCELLED => 'Cancelled',
        self::ENROLL => 'Enroll',
    ];
}
