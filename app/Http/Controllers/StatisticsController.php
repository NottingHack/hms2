<?php

namespace App\Http\Controllers;

use HMS\Views\LaserUsage;
use HMS\Views\MemberStats;
use HMS\Views\SnackspaceMonthly;
use HMS\Repositories\MetaRepository;
use Illuminate\Support\Facades\Cache;
use HMS\Repositories\Members\BoxRepository;
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
     * Create a new controller instance.
     *
     * @param ZoneRepository $zoneRepository
     * @param BoxRepository $boxRepository
     * @param MetaRepository $metaRepository
     */
    public function __construct(
        ZoneRepository $zoneRepository,
        BoxRepository $boxRepository,
        MetaRepository $metaRepository
    ) {
        $this->zoneRepository = $zoneRepository;
        $this->boxRepository = $boxRepository;
        $this->metaRepository = $metaRepository;
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

        return view('statistics.member_stats')
            ->with('memberStats', $memberStats);
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
}
