<?php

namespace HMS\Repositories\Tools;

use Carbon\Carbon;
use HMS\Entities\User;
use HMS\Entities\Tools\Tool;
use HMS\Entities\Tools\Usage;

interface UsageRepository
{
    /**
     * @param Tool $tool
     * @param Carbon $start
     * @param Carbon $end
     *
     * @return Usage[]
     */
    public function findByToolBetween(Tool $tool, Carbon $start, Carbon $end);

    /**
     * @param Tool $tool
     * @param Carbon $day
     *
     * @return Usage[]
     */
    public function findByToolForDay(Tool $tool, Carbon $day);

    /**
     * @param Tool $tool
     * @param Carbon $week
     *
     * @return Usage[]
     */
    public function findByToolForWeek(Tool $tool, Carbon $week);

    /**
     * @param Tool $tool
     * @param Carbon $month
     *
     * @return Usage[]
     */
    public function findByToolForMonth(Tool $tool, Carbon $month);

    /**
     * @param Tool $tool
     *
     * @return Usage[]
     */
    public function findByToolForThisWeek(Tool $tool);

    /**
     * Free/Pledge Time For Tool User
     *
     * @param Tool $tool
     * @param User $user
     *
     * @return string|null
     */
    public function freeTimeForToolUser(Tool $tool, User $user);

    /**
     * Save Usage to the DB.
     *
     * @param Usage $usage
     */
    public function save(Usage $usage);
}
