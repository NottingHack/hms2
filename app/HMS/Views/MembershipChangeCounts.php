<?php

namespace HMS\Views;

use Illuminate\Database\Eloquent\Model;

class MembershipChangeCounts extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'vw_role_update_counts';

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;
}
