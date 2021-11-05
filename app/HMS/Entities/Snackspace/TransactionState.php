<?php

namespace HMS\Entities\Snackspace;

abstract class TransactionState
{
    public const COMPLETE = 'COMPLETE';
    public const PENDING = 'PENDING';
    public const ABORTED = 'ABORTED';
}
