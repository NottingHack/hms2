<?php

namespace HMS\Entities\Snackspace;

abstract class TransactionType
{
    /*
     * Vending machine purchase.
     */
    public const VEND = 'VEND';

    /*
     * Transaction is a manually entered (via web interface) record of a payment or purchase.
     */
    public const MANUAL = 'MANUAL';

    /*
     * Tool usage.
     */
    public const TOOL = 'TOOL';

    /*
     * Heater usage.
     */
    public const HEAT = 'HEAT';

    /*
     * Purchase of a members box.
     */
    public const MEMBER_BOX = 'BOX';

    /*
     * Payment received by cash acceptor.
     */
    public const CASH_PAYMENT = 'CASHPAYMENT';

    /*
     * Payment made on-line (stripe).
     */
    public const ONLINE_PAYMENT = 'ONLINEPAYMENT';

    /*
     * Payment received by Direct Debit (GoCardless).
     */
    public const DD_PAYMENT = 'DDPAYMENT';

    /*
     * Payment received by Bank Transfer (direct).
     */
    public const BANK_PAYMENT = 'BANKPAYMENT';

    /**
     * String representation of types for display.
     */
    public const TYPE_STRINGS = [
        self::VEND => 'Vend',
        self::MANUAL => 'Manual',
        self::TOOL => 'Tool',
        self::MEMBER_BOX => 'Box',
        self::HEAT => 'Heat',
        self::CASH_PAYMENT => 'Cash Payment',
        self::ONLINE_PAYMENT => 'Online Payment',
        self::DD_PAYMENT => 'Direct Debit Payment',
        self::BANK_PAYMENT => 'Bank Transfer',
    ];
}
