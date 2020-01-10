<?php

namespace App\Jobs\GateKeeper;

use Carbon\Carbon;
use Carbon\CarbonInterval;
use Illuminate\Bus\Queueable;
use HMS\Entities\GateKeeper\Zone;
use Illuminate\Support\Facades\Log;
use HMS\Repositories\MetaRepository;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use App\Jobs\ZoneOccupantCountPublishJob;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use HMS\Entities\GateKeeper\ZoneOccupancyLog;
use HMS\Repositories\GateKeeper\ZoneRepository;
use HMS\Repositories\GateKeeper\ZoneOccupantRepository;
use HMS\Repositories\GateKeeper\ZoneOccupancyLogRepository;

class ZoneOccupantResetJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @param MetaRepository $metaRepository
     * @param ZoneRepository $zoneRepository
     * @param ZoneOccupantRepository $zoneOccupantRepository
     * @param ZoneOccupancyLogRepository $zoneOccupancyLogRepository
     *
     * @return void
     */
    public function handle(
        MetaRepository $metaRepository,
        ZoneRepository $zoneRepository,
        ZoneOccupantRepository $zoneOccupantRepository,
        ZoneOccupancyLogRepository $zoneOccupancyLogRepository
    ) {
        $resetUserCount = 0;
        $resetPeriod = CarbonInterval::instance(
            new \DateInterval($metaRepository->get('zone_occupant_reset_interval', 'P1D'))
        );
        $resetIfBeforeDate = Carbon::now()->sub($resetPeriod);
        $zones = $zoneRepository->findAll();
        $offSiteZone = $zoneRepository->findOneByShortName(Zone::OFF_SITE);

        foreach ($zones as $zone) {
            if ($zone->getShortName() == Zone::OFF_SITE) {
                continue;
            }
            $zoneOccupants = $zone->getZoneOccupancts();
            $currentCount = $zoneOccupants->count();

            foreach ($zoneOccupants as $zoneOccupant) {
                if ($zoneOccupant->getTimeEntered()->lessThan($resetIfBeforeDate)) {
                    $now = Carbon::now();
                    $user = $zoneOccupant->getUser();

                    // create new log rentry for old zone
                    $zoneOccupancyLog = (new ZoneOccupancyLog())
                        ->setZone($zone)
                        ->setUser($user)
                        ->setTimeEntered($zoneOccupant->getTimeEntered())
                        ->setTimeExited($now);

                    // change zone to offiste set enter time
                    $zoneOccupant->setZone($offSiteZone)
                        ->setTimeEntered($now);

                    // save them
                    $zoneOccupantRepository->save($zoneOccupant);
                    $zoneOccupancyLogRepository->save($zoneOccupancyLog);

                    $resetUserCount++;
                    $currentCount--;
                }
            }

            // MQTT pub this zone
            ZoneOccupantCountPublishJob::dispatch($zone, $currentCount);
        }

        Log::info('ZoneOccupantResetJob: Reset Zone for ' . $resetUserCount . ' users.');
    }
}
