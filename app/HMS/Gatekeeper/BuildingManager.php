<?php

namespace HMS\Gatekeeper;

use HMS\Entities\Role;
use HMS\Entities\Gatekeeper\Building;
use HMS\Entities\Gatekeeper\BuildingAccessState;
use HMS\Repositories\Gatekeeper\BuildingRepository;
use App\Jobs\Gatekeeper\AddZonePermissionForBuilding;

class BuildingManager
{
    /**
     * @var BuildingRepository
     */
    protected $buildingRepository;

    /**
     * @var TemporaryAccessBookingManager
     */
    protected $temporaryAccessBookingManager;

    /**
     * Create a new manager instance.
     *
     * @param BuildingRepository $buildingRepository
     * @param TemporaryAccessBookingManager $temporaryAccessBookingManager
     */
    public function __construct(
        BuildingRepository $buildingRepository,
        TemporaryAccessBookingManager $temporaryAccessBookingManager
    ) {
        $this->buildingRepository = $buildingRepository;
        $this->temporaryAccessBookingManager = $temporaryAccessBookingManager;
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
        AddZonePermissionForBuilding::dispatch($building);

        // remove all future TemporaryAccessBookings
        $this->temporaryAccessBookingManager->removeAllFutureBookingsForBuilding($building);

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
        RemoveZonePermissionForBuilding::dispatch($building);

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
        RemoveZonePermissionForBuilding::dispatch($building);

        // unapprove any future TemporaryAccessBookings, unless User can gatekeeper.access.manage
        $this->temporaryAccessBookingManager->unapproveFutureBookingsForBuildingUnlessManger($building);

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
        RemoveZonePermissionForBuilding::dispatch($building);

        // remove any future TemporaryAccessBookings, unless User can gatekeeper.access.manage
        $this->temporaryAccessBookingManager->removeFutureBookingsForBuildingUnlessManager($building);

        // lastly set the state on the building
        $building->setAccessState(BuildingAccessState::CLOSED);
        $this->buildingRepository->save($building);

        return $building;
    }
}
