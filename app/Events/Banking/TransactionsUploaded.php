<?php

namespace App\Events\Banking;

use Illuminate\Queue\SerializesModels;

class TransactionsUploaded
{
    use SerializesModels;

    /**
     * @var array
     */
    public $transactions;

    /**
     * Create a new event instance.
     *
     * @param array $transactions
     */
    public function __construct($transactions)
    {
        $this->transactions = $transactions;
    }
}
