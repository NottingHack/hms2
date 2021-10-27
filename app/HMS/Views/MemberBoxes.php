<?php

namespace HMS\Views;

use Illuminate\Database\Eloquent\Model;

class MemberBoxes extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'vw_member_boxes';

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;
}
