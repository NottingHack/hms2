<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use HMS\Views\LaserUsage;
use HMS\Views\MemberStats;
use HMS\Views\SnackspaceMonthly;
use HMS\Governance\VotingManager;
use HMS\Repositories\MetaRepository;
use HMS\Repositories\RoleRepository;
use Illuminate\Support\Facades\Cache;
use HMS\Repositories\Tools\ToolRepository;
use HMS\Repositories\Members\BoxRepository;
use HMS\Repositories\Tools\UsageRepository;
use HMS\Repositories\Tools\BookingRepository;
use HMS\Repositories\GateKeeper\ZoneRepository;

class StatisticsController extends Controller
{
    /**
     * @var ZoneRepository
     */
    protected $zoneRepository;

    /**
     * @var BoxRepository
     */
    protected $boxRepository;

    /**
     * @var MetaRepository
     */
    protected $metaRepository;

    /**
     * @var VotingManager
     */
    protected $votingManager;

    /**
     * @var ToolRepository
     */
    protected $toolRepository;

    /**
     * @var RoleRepository
     */
    protected $roleRepository;

    /**
     * @var BookingRepository
     */
    protected $bookingRepository;

    /**
     * @var UsageRepository
     */
    protected $usageRepository;

    /**
     * Create a new controller instance.
     *
     * @param ZoneRepository $zoneRepository
     * @param BoxRepository $boxRepository
     * @param MetaRepository $metaRepository
     * @param VotingManager $votingManager
     * @param ToolRepository $toolRepository
     * @param RoleRepository $roleRepository
     * @param BookingRepository $bookingRepository
     * @param UsageRepository $usageRepository
     */
    public function __construct(
        ZoneRepository $zoneRepository,
        BoxRepository $boxRepository,
        MetaRepository $metaRepository,
        VotingManager $votingManager,
        ToolRepository $toolRepository,
        RoleRepository $roleRepository,
        BookingRepository $bookingRepository,
        UsageRepository $usageRepository
    ) {
        $this->zoneRepository = $zoneRepository;
        $this->boxRepository = $boxRepository;
        $this->metaRepository = $metaRepository;
        $this->votingManager = $votingManager;
        $this->toolRepository = $toolRepository;
        $this->roleRepository = $roleRepository;
        $this->bookingRepository = $bookingRepository;
        $this->usageRepository = $usageRepository;
    }

    /**
     * Show statistics view for boxes.
     *
     * @return \Illuminate\Http\Response
     */
    public function boxUsage()
    {
        $total = $this->boxRepository->count();
        $inUse = $this->boxRepository->countAllInUse();
        $removed = $this->boxRepository->countAllRemoved();
        $abandoned = $this->boxRepository->countAllAbandoned();
        $totalSpaces = $this->metaRepository->get('member_box_limit');

        return view('statistics.members_boxes')
            ->with(
                [
                    'total' => $total,
                    'inUse' => $inUse,
                    'removed' => $removed,
                    'abandoned' => $abandoned,
                    'totalSpaces' => $totalSpaces,
                ]
            );
    }

    /**
     * Show lasge usage statistics view.
     *
     * @return \Illuminate\Http\Response
     */
    public function laserUsage()
    {
        // pull stats from the cache if they are there, if not store a fresh copy for 1 day
        $laserUsage = Cache::remember('statistics.laserUsage', 86400, function () {
            config(['database.connections.mysql.strict' => false]);
            \DB::reconnect(); //important as the existing connection if any would be in strict mode
            $laserUsage = LaserUsage::all();

            //now changing back the strict ON
            config(['database.connections.mysql.strict' => true]);
            \DB::reconnect();

            return $laserUsage;
        });

        return view('statistics.laser_usage')
            ->with('laserUsage', $laserUsage);
    }

    /**
     * Show membership statistics view.
     *
     * @return \Illuminate\Http\Response
     */
    public function memberStats()
    {
        // pull stats from the cache if they are there, if not store a fresh copy for 1 day
        $memberStats = Cache::remember('statistics.membership', 86400, function () {
            return MemberStats::first();
        });

        $votingMembers = $this->votingManager->countVotingMembers();

        return view('statistics.member_stats')
            ->with('memberStats', $memberStats)
            ->with('votingMembers', $votingMembers);
    }

    /**
     * Show membership statistics view.
     *
     * @return \Illuminate\Http\Response
     */
    public function snackspaceMonthly()
    {
        // pull stats from the cache if they are there, if not store a fresh copy for 1 day
        $snackspaceMonthly = Cache::remember('statistics.snackspace-monthly', 86400, function () {
            config(['database.connections.mysql.strict' => false]);
            \DB::reconnect(); //important as the existing connection if any would be in strict mode
            $snackspaceMonthly = SnackspaceMonthly::all();

            //now changing back the strict ON
            config(['database.connections.mysql.strict' => true]);
            \DB::reconnect();

            return $snackspaceMonthly;
        });

        return view('statistics.snackspace_monthly')
            ->with('snackspaceMonthly', $snackspaceMonthly);
    }

    /**
     * Show zone occupancy view.
     *
     * @return \Illuminate\Http\Response
     */
    public function zoneOccupancy()
    {
        $zones = $this->zoneRepository->findAll();

        return view('statistics.zone_occupants')
            ->with('zones', $zones);
    }

    /**
     * Show tools statistics view.
     *
     * @return \Illuminate\Http\Response
     */
    public function tools()
    {
        // pull stats from the cache if they are there, if not store a fresh copy for 12 hours
        $tools = Cache::remember('statistics.tools', 43200, function () {
            return $this->generateToolStats();
        });

        return view('statistics.tools')
            ->with('tools', $tools);
    }

    /**
     * Generate Statistics for Tool.
     *
     * @return array
     */
    protected function generateToolStats()
    {
        $tools = $this->toolRepository->findAll();
        $stats = [];

        foreach ($tools as $tool) {
            $userCount = $this->roleRepository
                ->findOneByName('tools.' . $tool->getPermissionName() . '.user')
                ->getUsers()
                ->count();
            $inductorCount = $this->roleRepository
                ->findOneByName('tools.' . $tool->getPermissionName() . '.inductor')
                ->getUsers()
                ->count();
            $maintainerCount = $this->roleRepository
                ->findOneByName('tools.' . $tool->getPermissionName() . '.maintainer')
                ->getUsers()
                ->count();

            $bookingsForThisMonth = $this->bookingRepository->findByToolForMonth($tool, Carbon::now());
            $bookingsForLastMonth = $this->bookingRepository->findByToolForMonth($tool, Carbon::now()->subMonth());

            $bookedThisMonth = $this->countBookedDuration($bookingsForThisMonth);
            $bookedLastMonth = $this->countBookedDuration($bookingsForLastMonth);

            $usagesForThisMonth = $this->usageRepository->findByToolForMonth($tool, Carbon::now());
            $usagesForLastMonth = $this->usageRepository->findByToolForMonth($tool, Carbon::now()->subMonth());

            $usedThisMonth = $this->countUsedMinutes($usagesForThisMonth);
            $usedLastMonth = $this->countUsedMinutes($usagesForLastMonth);

            $stats[$tool->getName()] = [
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

        return sprintf('%02d:%02d:%02d', ($seconds / 3600), ($seconds / 60 % 60), $seconds % 60);
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

        return sprintf('%02d:%02d:%02d', ($seconds / 3600), ($seconds / 60 % 60), $seconds % 60);
    }
}
