<?php

namespace HMS\Views;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'vw_payment';

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;
}
