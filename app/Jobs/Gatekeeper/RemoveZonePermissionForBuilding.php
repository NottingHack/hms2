<?php

namespace App\Jobs\Gatekeeper;

use HMS\Entities\Gatekeeper\Building;
use HMS\Entities\Role;
use HMS\Repositories\Gatekeeper\BuildingRepository;
use HMS\Repositories\PermissionRepository;
use HMS\Repositories\RoleRepository;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class RemoveZonePermissionForBuilding implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var int
     */
    protected $buildingId;

    /**
     * Create a new job instance.
     *
     * @param Building $building
     *
     * @return void
     */
    public function __construct(Building $building)
    {
        // we are just going ot have to pull a fresh copy from the db anyway
        $this->buildingId = $building->getId();
    }

    /**
     * Given a building with zones remove there permission form Role::MEMBER_CURRENT to stop unbooked User access.
     *
     * @param BuildingRepository $buildingRepository
     * @param RoleRepository $roleRepository
     * @param PermissionRepository $permissionRepository
     *
     * @return void
     */
    public function handle(
        BuildingRepository $buildingRepository,
        RoleRepository $roleRepository,
        PermissionRepository $permissionRepository
    ) {
        $building = $buildingRepository->findOneById($this->buildingId);
        $currentRole = $roleRepository->findOneByName(Role::MEMBER_CURRENT);

        foreach ($building->getZones() as $zone) {
            $zonePermission = $permissionRepository->findOneByName($zone->getPermissionCode());
            $currentRole->removePermission($zonePermission);
        }

        $roleRepository->save($currentRole);
    }
}
