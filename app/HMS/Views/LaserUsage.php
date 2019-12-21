<?php

namespace HMS\Views;

use Illuminate\Database\Eloquent\Model;

class LaserUsage extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'vw_laser_usage';

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;
}
