<?php

namespace HMS\Views;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $year
 * @property string $month
 * @property string $total_time
 * @property string $a0_time
 * @property string $a2_time
 * @property string $charged_time
 * @property string $charged_income
 * @property int $distinct_users
 * @property int $members_inducted
 */
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
