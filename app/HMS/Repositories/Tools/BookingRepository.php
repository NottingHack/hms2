<?php

namespace HMS\Repositories\Tools;

use Carbon\Carbon;
use HMS\Entities\User;
use HMS\Entities\Tools\Tool;
use HMS\Entities\Tools\Booking;

interface BookingRepository
{
    /**
     * Get the current booking for a tool.
     * @param  Tool   $tool
     * @return null|Booking
     */
    public function currnetForTool(Tool $tool);

    /**
     * Get the next booking for a tool.
     * @param  Tool   $tool
     * @return null|Booking
     */
    public function nextForTool(Tool $tool);

    /**
     * Check for any Bookings that would clash with a given start and end time.
     *
     * @param  Tool   $tool
     * @param  Carbon $start
     * @param  Carbon $end
     * @return Bookings[]
     */
    public function checkForClashByTool(Tool $tool, Carbon $start, Carbon $end);

    /**
     * @param Tool $tool
     * @return Booking[]
     */
    public function findByTool(Tool $tool);

    /**
     * @param  Tool   $tool
     * @param  User   $user
     * @return Booking[]
     */
    public function findByToolAndUser(Tool $tool, User $user);

    /**
     * Count normal booing for a User on a given Tool.
     *
     * @param  Tool   $tool
     * @param  User   $user
     * @param  BookingType $bookingType
     * @return Booking[]
     */
    public function countNormalByToolAndUser(Tool $tool, User $user);

    /**
     * @param Tool $tool
     * @param Carbon $start
     * @param Carbon $end
     * @return Booking[]
     */
    public function findByToolBetween(Tool $tool, Carbon $start, Carbon $end);

    /**
     * @param Tool $tool
     * @return Booking[]
     */
    public function findNormalByTool(Tool $tool);

    /**
     * @param Tool $tool
     * @return Booking[]
     */
    public function findInductionByTool(Tool $tool);

    /**
     * @param Tool $tool
     * @return Booking[]
     */
    public function findMaintenanceByTool(Tool $tool);

    /**
     * save Booking to the DB.
     * @param  Booking $booking
     */
    public function save(Booking $booking);

    /**
     * Remove a Booking.
     * @param  Booking $booking
     */
    public function remove(Booking $booking);
}
