<?php

namespace App\HMS\Views;

use Illuminate\Database\Eloquent\Model;

class LowLastPaymentAmount extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'vw_low_last_payment_amount';

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'last_payment_date' => 'date',
    ];
}
