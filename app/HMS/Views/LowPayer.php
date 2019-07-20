<?php

namespace HMS\Views;

use Illuminate\Database\Eloquent\Model;

class LowPayer extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'vw_low_payers';

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;
}
