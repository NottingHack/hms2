<?php

namespace HMS\Entities\Phones;

abstract class ExtensionType
{
    /*
     * SIP Extension
     */
    public const SIP = 'SIP';

    /*
     * DECT Extension
     */
    public const DECT = 'DECT';

    /*
     * POTS Extension
     */
    public const POTS = 'POTS';

    /*
     * Reservation for custom Asterisk handler
     */
    public const CUSTOM = 'CUSTOM';

    /**
     * String representations of each type.
     */
    public const TYPE_STRINGS = [
        self::SIP => 'SIP (VoIP)',
        self::DECT => 'DECT (Wireless Handset)',
        self::POTS => 'POTS (Analogue Voice via Copper Pair)',
        self::CUSTOM => 'Custom Asterisk Extension',
    ];
}
