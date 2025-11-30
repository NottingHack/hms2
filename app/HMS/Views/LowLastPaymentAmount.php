<?php

namespace App\HMS\Views;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $account_id
 * @property string $payment_ref
 * @property \Illuminate\Support\Carbon $last_payment_date
 * @property int $amount
 * @property string $amount_pounds
 * @property int $joint_count
 * @property string $amount_joint_adjusted
 */
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
     * @var array<string, string>
     */
    protected $casts = [
        'last_payment_date' => 'date',
    ];
}
