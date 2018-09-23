<?php

namespace HMS\Entities\Snackspace;

abstract class TransactionType
{
    const VEND = 'VEND';     // Transaction relates to either vending machine purchace, or a payment received by note acceptor
    const MANUAL = 'MANUAL'; // Transaction is a manually entered (via web interface) record of a payment or purchase
    const TOOL = 'TOOL';
    const MEMBER_BOX = 'BOX';
}
