<?php

namespace App\Jobs\Gatekeeper;

use Carbon\Carbon;
use HMS\Entities\User;
use Illuminate\Bus\Queueable;
use HMS\Entities\Gatekeeper\Zone;
use HMS\Entities\Gatekeeper\Building;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use HMS\Entities\Gatekeeper\ZoneOccupancyLog;
use HMS\Repositories\Gatekeeper\ZoneRepository;
use HMS\Repositories\Gatekeeper\ZoneOccupantRepository;
use HMS\Repositories\Gatekeeper\ZoneOccupancyLogRepository;

class UserHasLeftTheBuildingJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var Building
     */
    protected $building;

    /**
     * @var User
     */
    protected $user;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Building $building, User $user)
    {
        $this->building = $building;
        $this->user = $user;
    }

    /**
     * Execute the job.
     *
     * @param ZoneRepository $zoneRepository
     * @param ZoneOccupantRepository $zoneOccupantRepository
     * @param ZoneOccupancyLogRepository $zoneOccupancyLogRepository
     *
     * @return void
     */
    public function handle(
        ZoneRepository $zoneRepository,
        ZoneOccupantRepository $zoneOccupantRepository,
        ZoneOccupancyLogRepository $zoneOccupancyLogRepository
    ) {
        $offSiteZone = $zoneRepository->findOneByShortName(Zone::OFF_SITE);
        $zoneOccupant = $zoneOccupantRepository->findOneByUser($this->user);
        $oldZone = $zoneOccupant->getZone();
        $now = Carbon::now();
        $user = $zoneOccupant->getUser(); // get the fresh user off the ZoneOccupant

        if ($oldZone->getShortName() == Zone::OFF_SITE) {
            // oh you are already off site :)
            return;
        }

        // check the we still think there are in the correct building
        if ($oldZone->getBuilding()->getId() != $this->building->getId()) {
            //not in the expected building any more
            return;
        }

        // reset them back to the outside world
        // create new log rentry for old zone
        $zoneOccupancyLog = (new ZoneOccupancyLog())
            ->setZone($oldZone)
            ->setUser($user)
            ->setTimeEntered($zoneOccupant->getTimeEntered())
            ->setTimeExited($now);

        // change zone to offiste set enter time
        $zoneOccupant->setZone($offSiteZone)
            ->setTimeEntered($now);

        // save them
        $zoneOccupantRepository->save($zoneOccupant);
        $zoneOccupancyLogRepository->save($zoneOccupancyLog);
    }
}
