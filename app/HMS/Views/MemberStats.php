<?php

namespace HMS\Views;

use Illuminate\Database\Eloquent\Model;

class MemberStats extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'vw_member_stats';

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;
}
