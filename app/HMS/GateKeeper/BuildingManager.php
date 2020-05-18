<?php

namespace HMS\GateKeeper;

use HMS\Entities\Role;
use HMS\Repositories\RoleRepository;
use HMS\Entities\GateKeeper\Building;
use HMS\Repositories\PermissionRepository;
use HMS\Entities\GateKeeper\BuildingAccessState;
use HMS\Repositories\GateKeeper\BuildingRepository;

class BuildingManager
{
    /**
     * @var BuildingRepository
     */
    protected $buildingRepository;

    /**
     * @var RoleRepository
     */
    protected $roleRepository;

    /**
     * @var PermissionRepository
     */
    protected $permissionRepository;

    /**
     * Create a new manager instance.
     *
     * @param BuildingRepository $buildingRepository
     * @param RoleRepository $roleRepository
     * @param PermissionRepository $permissionRepository
     */
    public function __construct(
        BuildingRepository $buildingRepository,
        RoleRepository $roleRepository,
        PermissionRepository $permissionRepository
    ) {
        $this->buildingRepository = $buildingRepository;
        $this->roleRepository = $roleRepository;
        $this->permissionRepository = $permissionRepository;
    }

    /**
     * Update Self Booking Max Occupancy for the building.
     *
     * @param Building $building
     * @param int $selfBookMaxOccupancy
     *
     * @return string|Building
     */
    public function updateOccupancy(Building $building, int $selfBookMaxOccupancy)
    {
        if ($selfBookMaxOccupancy < 1) {
            return 'The self booking max occupancy must be at least 1.';
        }

        $building->setSelfBookMaxOccupancy($selfBookMaxOccupancy);
        $this->buildingRepository->save($building);

        return $building;
    }

    /**
     * Update Access State for the building.
     *
     * @param Building $building
     * @param int $accessState
     *
     * @return string|Building
     */
    public function updateAccessState(Building $building, $accessState)
    {
        switch ($accessState) {
            case BuildingAccessState::FULL_OPEN:
                $response = $this->setAccessFullOpen($building);
                break;

            case BuildingAccessState::SELF_BOOK:
                $response = $this->setAccessSelfBook($building);
                break;
            case BuildingAccessState::REQUESTED_BOOK:
                $response = $this->setAccessRequstBook($building);
                break;
            case BuildingAccessState::CLOSED:
                $response = $this->setAccessClosed($building);
                break;
            default:
                $response = 'Invalid access state requested.';
                break;
        }

        return $response;
    }

    /**
     * Update Access State to Fully Open.
     *
     * @param Building $building
     *
     * @return string|Building
     */
    public function setAccessFullOpen(Building $building)
    {
        // Make sure Role::MEMBER_CURRENT has the correct zone permissions to allow User access
        $this->addZonePermissionForBuilding($building);

        // remove all futuer TemporaryAccessBookings

        // lastly set the state on the building
        $building->setAccessState(BuildingAccessState::FULL_OPEN);
        $this->buildingRepository->save($building);

        return $building;
    }

    /**
     * Update Access State to Booked access.
     *
     * @param Building $building
     *
     * @return string|Building
     */
    public function setAccessSelfBook(Building $building)
    {
        // Make sure Role::MEMBER_CURRENT zone permissions have been removed to stop unbooked User access
        $this->removeZonePermissionForBuilding($building);

        // lastly set the state on the building
        $building->setAccessState(BuildingAccessState::SELF_BOOK);
        $this->buildingRepository->save($building);

        return $building;
    }

    /**
     * Update Access State to Requested access.
     *
     * @param Building $building
     *
     * @return string|Building
     */
    public function setAccessRequstBook(Building $building)
    {
        // Make sure Role::MEMBER_CURRENT zone permissions have been removed to stop unbooked User access
        $this->removeZonePermissionForBuilding($building);

        // unapporved any futuer TemporaryAccessBookings, unless User can gatekeeper.access.manage

        // lastly set the state on the building
        $building->setAccessState(BuildingAccessState::REQUESTED_BOOK);
        $this->buildingRepository->save($building);

        return $building;
    }

    /**
     * Update Access State to Closed.
     *
     * @param Building $building
     *
     * @return string|Building
     */
    public function setAccessClosed(Building $building)
    {
        // Make sure Role::MEMBER_CURRENT zone permissions have been removed to stop unbooked User access
        $this->removeZonePermissionForBuilding($building);

        // remove any futuer TemporaryAccessBookings, unless User can gatekeeper.access.manage

        // lastly set the state on the building
        $building->setAccessState(BuildingAccessState::CLOSED);
        $this->buildingRepository->save($building);

        return $building;
    }

    /**
     * Given a building with zones add there permission to Role::MEMBER_CURRENT to allow User access.
     *
     * @param Building $building
     */
    protected function addZonePermissionForBuilding(Building $building)
    {
        $currentRole = $this->roleRepository->findOneByName(Role::MEMBER_CURRENT);

        foreach ($building->getZones() as $zone) {
            $zonePermission = $this->permissionRepository->findOneByName($zone->getPermissionCode());
            $currentRole->addPermission($zonePermission);
        }

        $this->roleRepository->save($currentRole);

        return $building;
    }

    /**
     * Given a building with zones remove there permission form Role::MEMBER_CURRENT to stop unbooked User access.
     *
     * @param Building $building
     */
    protected function removeZonePermissionForBuilding(Building $building)
    {
        $currentRole = $this->roleRepository->findOneByName(Role::MEMBER_CURRENT);

        foreach ($building->getZones() as $zone) {
            $zonePermission = $this->permissionRepository->findOneByName($zone->getPermissionCode());
            $currentRole->removePermission($zonePermission);
        }

        $this->roleRepository->save($currentRole);

        return $building;
    }
}
