<?php

namespace HMS\Entities\Snackspace;

abstract class TransactionType
{
    /*
     * Vending machine purchase.
     */
    const VEND = 'VEND';

    /*
     * Transaction is a manually entered (via web interface) record of a payment or purchase.
     */
    const MANUAL = 'MANUAL';

    /*
     * Tool usage.
     */
    const TOOL = 'TOOL';

    /*
     * Purchase of a members box.
     */
    const MEMBER_BOX = 'BOX';

    /*
     * Payment received by cash acceptor.
     */
    const CASH_PAYMENT = 'CASHPAYMENT';

    /*
     * Payment made on-line (stripe).
     */
    const ONLINE_PAYMENT = 'ONLINEPAYMENT';

    /*
     * Payment received by Direct Debit (GoCardless).
     */
    const DD_PAYMENT = 'DDPAYMENT';

    /**
     * String representation of types for display.
     */
    const TYPE_STRINGS = [
        self::VEND => 'Vend',
        self::MANUAL => 'Manual',
        self::TOOL => 'Tool',
        self::MEMBER_BOX => 'Box',
        self::CASH_PAYMENT => 'Cash Payment',
        self::ONLINE_PAYMENT => 'Online Payment',
        self::DD_PAYMENT => 'Direct Debit Payment',
    ];
}
