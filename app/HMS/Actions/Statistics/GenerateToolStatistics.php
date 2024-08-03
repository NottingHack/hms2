<?php

namespace HMS\Actions\Statistics;

use Carbon\Carbon;
use HMS\Entities\Role;
use HMS\Repositories\RoleRepository;
use HMS\Repositories\Tools\BookingRepository;
use HMS\Repositories\Tools\ToolRepository;
use HMS\Repositories\Tools\UsageRepository;
use Illuminate\Support\Facades\Cache;

class GenerateToolStatistics
{
    public function execute()
    {
        return Cache::remember('statistics.tools', 3600 * 6, function () {
            return app()->call([$this, 'generateToolStats']);
        });
    }

    /**
     * Generate Statistics for Tool.
     *
     * @return array
     */
    public function generateToolStats(
        ToolRepository $toolRepository,
        RoleRepository $roleRepository,
        BookingRepository $bookingRepository,
        UsageRepository $usageRepository,
    ) {
        $tools = $toolRepository->findAll();
        $stats = [];

        foreach ($tools as $tool) {
            $users = $roleRepository
                ->findOneByName('tools.' . $tool->getPermissionName() . '.user')
                ->getUsers();
            $currentUsers = $users->filter(function ($user) {
                return $user->hasRoleByName(Role::MEMBER_CURRENT);
            });
            $userCount = $currentUsers->count();

            $inductorCount = $roleRepository
                ->findOneByName('tools.' . $tool->getPermissionName() . '.inductor')
                ->getUsers()
                ->count();
            $maintainerCount = $roleRepository
                ->findOneByName('tools.' . $tool->getPermissionName() . '.maintainer')
                ->getUsers()
                ->count();

            $bookingsForThisMonth = $bookingRepository->findByToolForMonth($tool, Carbon::now());
            $bookingsForLastMonth = $bookingRepository->findByToolForMonth($tool, Carbon::now()->subMonthNoOverflow());

            $bookedThisMonth = $this->countBookedDuration($bookingsForThisMonth);
            $bookedLastMonth = $this->countBookedDuration($bookingsForLastMonth);

            $usagesForThisMonth = $usageRepository->findByToolForMonth($tool, Carbon::now());
            $usagesForLastMonth = $usageRepository->findByToolForMonth($tool, Carbon::now()->subMonthNoOverflow());

            $usedThisMonth = $this->countUsedMinutes($usagesForThisMonth);
            $usedLastMonth = $this->countUsedMinutes($usagesForLastMonth);

            $stats[$tool->getDisplayName()] = [
                'userCount' => $userCount,
                'inductorCount' => $inductorCount,
                'maintainerCount' => $maintainerCount,
                'bookedThisMonth' => $bookedThisMonth,
                'bookedLastMonth' => $bookedLastMonth,
                'usedThisMonth' => $usedThisMonth,
                'usedLastMonth' => $usedLastMonth,
            ];
        }

        return $stats;
    }

    /**
     * Sum Booking lengths.
     *
     * @param Booking[] $bookings
     *
     * @return string
     */
    protected function countBookedDuration($bookings)
    {
        $seconds = 0;

        foreach ($bookings as $booking) {
            $seconds += $booking->getStart()->diffInSeconds($booking->getEnd());
        }

        return sprintf('%02d:%02d:%02d', $seconds / 3600, $seconds / 60 % 60, $seconds % 60);
    }

    /**
     * Sum Usage durations.
     *
     * @param Usage[] $usages
     *
     * @return string
     */
    protected function countUsedMinutes($usages)
    {
        $seconds = 0;

        foreach ($usages as $usage) {
            if ($usage->getDuration() > 0) {
                $seconds += $usage->getDuration();
            }
        }

        return sprintf('%02d:%02d:%02d', $seconds / 3600, $seconds / 60 % 60, $seconds % 60);
    }
}
