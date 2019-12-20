<?php

namespace App\Http\Controllers;

use HMS\Views\MemberStats;
use HMS\Views\SnackspaceMonthly;
use Illuminate\Support\Facades\Cache;
use HMS\Repositories\GateKeeper\ZoneRepository;

class StatisticsController extends Controller
{
    /**
     * @var ZoneRepository
     */
    protected $zoneRepository;

    /**
     * Create a new controller instance.
     *
     * @param ZoneRepository $zoneRepository
     */
    public function __construct(ZoneRepository $zoneRepository)
    {
        $this->zoneRepository = $zoneRepository;
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
            return \HMS\Views\MemberStats::first();
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
