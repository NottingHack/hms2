<?php

namespace HMS\Views;

use Illuminate\Database\Eloquent\Model;

/**
 * @property string $date
 * @property int $current_added
 * @property int $current_removed
 * @property int $young_added
 * @property int $young_removed
 * @property int $ex_added
 * @property int $ex_removed
 * @property int $temporarybanned_added
 * @property int $temporarybanned_removed
 * @property int $banned_added
 * @property int $banned_removed
 */
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
