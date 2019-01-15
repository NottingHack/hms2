<?php

namespace HMS\Entities\Snackspace;

abstract class TransactionType
{
    const VEND = 'VEND';     // Transaction relates to either vending machine purchace, or a payment received by note acceptor
    const MANUAL = 'MANUAL'; // Transaction is a manually entered (via web interface) record of a payment or purchase
    const TOOL = 'TOOL';
    const MEMBER_BOX = 'BOX';
    const CASH_PAYMENT = 'CASHPAYMENT';
    const ONLINE_PAYMENT = 'ONLINEPAYMENT';
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
