<?php

namespace HMS\Entities\Banking\Stripe;

abstract class ChargeType
{
    /*
     * Payment for Snackspace.
     */
    public const SNACKSPACE = 'SNACKSPACE';

    /*
     * Donation to the space.
     */
    public const DONATION = 'DONATION';

    /**
     * String representation of types for display.
     */
    public const TYPE_STRINGS = [
        self::SNACKSPACE => 'Snackspace',
        self::DONATION => 'Donation',
    ];
}
