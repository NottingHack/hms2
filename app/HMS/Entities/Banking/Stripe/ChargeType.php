<?php

namespace HMS\Entities\Banking\Stripe;

abstract class ChargeType
{
    /*
     * Payment for Snackspace.
     */
    const SNACKSPACE = 'SNACKSPACE';

    /*
     * Donation to the space.
     */
    const DONATION = 'DONATION';

    /**
     * String representation of types for display.
     */
    const TYPE_STRINGS = [
        self::SNACKSPACE => 'Snackspace',
        self::DONATION => 'Donation',
    ];
}
