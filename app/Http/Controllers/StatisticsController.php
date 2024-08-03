<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use HMS\Actions\Statistics\GenerateToolStatistics;
use HMS\Entities\Role;
use HMS\Governance\VotingManager;
use HMS\Repositories\EmailRepository;
use HMS\Repositories\Gatekeeper\ZoneRepository;
use HMS\Repositories\InviteRepository;
use HMS\Repositories\Members\BoxRepository;
use HMS\Repositories\MetaRepository;
use HMS\Repositories\RoleRepository;
use HMS\Repositories\RoleUpdateRepository;
use HMS\Views\LaserUsage;
use HMS\Views\MemberStats;
use HMS\Views\SnackspaceMonthly;
use Illuminate\Support\Facades\Cache;

class StatisticsController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct(
        protected ZoneRepository $zoneRepository,
        protected BoxRepository $boxRepository,
        protected MetaRepository $metaRepository,
        protected VotingManager $votingManager,
        protected RoleRepository $roleRepository,
        protected EmailRepository $emailRepository,
        protected InviteRepository $inviteRepository,
        protected RoleUpdateRepository $roleUpdateRepository,
        protected GenerateToolStatistics $generateToolStatisticsAction,
    ) {
        $this->middleware('feature:boxes')->only(['boxUsage']);
        $this->middleware('feature:tools')->only(['laserUsage']);
        $this->middleware('feature:snackspace')->only(['snackspaceMonthly']);
        $this->middleware('feature:tools')->only(['tools']);
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

        $memberConversionStats = Cache::remember('statistics.membershipConversion', 86400, function () {
            return $this->membershipConversionStats();
        });

        return view('statistics.member_stats')
            ->with('memberStats', $memberStats)
            ->with('votingMembers', $votingMembers)
            ->with($memberConversionStats);
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

        $resetPeriod = new \DateInterval($this->metaRepository->get('zone_occupant_reset_interval', 'P1D'));

        $days = $resetPeriod->format('%d');
        $hours = 0;
        if ($days) {
            $hours += 24 * $days;
        }
        $hours += $resetPeriod->format('%H');

        return view('statistics.zone_occupants')
            ->with('resetHours', $hours)
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
        $tools = $this->generateToolStatisticsAction->execute();

        return view('statistics.tools')
            ->with('tools', $tools);
    }

    /**
     * Calculate some membership conversion statistics.
     *
     * @return array
     */
    protected function membershipConversionStats()
    {
        $monthAgo = Carbon::now()->subMonthNoOverflow();
        $weekAgo = Carbon::now()->subWeek();

        $ism = $this->emailRepository->countSentAfterWithSubject(
            $monthAgo,
            config('branding.space_name') . ': Interest registered'
        );
        $isw = $this->emailRepository->countSentAfterWithSubject(
            $weekAgo,
            config('branding.space_name') . ': Interest registered'
        );

        $iom = $this->inviteRepository->findCreatedBetween($monthAgo, Carbon::now());
        $iow = $this->inviteRepository->findCreatedBetween($weekAgo, Carbon::now());

        $apr = $this->roleRepository->findOneByName(Role::MEMBER_PAYMENT);
        $apu = $apr->getUsers();
        $rur = $this->roleUpdateRepository;
        $apm = $apu->filter(function ($user) use ($apr, $rur, $monthAgo) {
            $ru = $rur->findLatestRoleAddedByUser($apr, $user);

            return $ru->getCreatedAt()->isAfter($monthAgo);
        });
        $apw = $apu->filter(function ($user) use ($apr, $rur, $weekAgo) {
            $ru = $rur->findLatestRoleAddedByUser($apr, $user);

            return $ru->getCreatedAt()->isAfter($weekAgo);
        });

        $cmr = $this->roleRepository->findOneByName(Role::MEMBER_CURRENT);
        $cmu = $cmr->getUsers();
        $cmm = $cmu->filter(function ($user) use ($monthAgo) {
            if (is_null($user->getProfile()->getJoinDate())) {
                return false;
            }

            return $user->getProfile()->getJoinDate()->isAfter($monthAgo);
        });
        $cmw = $cmu->filter(function ($user) use ($weekAgo) {
            if (is_null($user->getProfile()->getJoinDate())) {
                return false;
            }

            return $user->getProfile()->getJoinDate()->isAfter($weekAgo);
        });

        return [
            'invitesSentLastWeek' => $isw,
            'invitesSentLastMonth' => $ism,

            'invitesOutstandingLastWeek' => count($iow),
            'invitesOutstandingLastMonth' => count($iom),

            'awaitingPaymentLastWeek' => count($apw),
            'awaitingPaymentLastMonth' => count($apm),

            'newMembersLastWeek' => count($cmw),
            'newMembersLastMonth' => count($cmm),
        ];
    }
}
