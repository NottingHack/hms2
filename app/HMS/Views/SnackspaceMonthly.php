<?php

namespace HMS\Views;

use Illuminate\Database\Eloquent\Model;

class SnackspaceMonthly extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'vw_snackspace_monthly';

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;
}
