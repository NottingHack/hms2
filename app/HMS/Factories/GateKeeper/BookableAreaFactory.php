<?php

namespace HMS\Factories\GateKeeper;

use HMS\Entities\GateKeeper\BookableArea;
use HMS\Repositories\GateKeeper\BuildingRepository;
use HMS\Repositories\GateKeeper\BookableAreaRepository;

class BookableAreaFactory
{
    /**
     * @var BookableAreaRepository
     */
    protected $bookableAreaRepository;

    /**
     * @var BuildingRepository
     */
    protected $buildingRepository;

    /**
     * @param BookableAreaRepository $bookableAreaRepository
     * @param BuildingRepository $buildingRepository
     */
    public function __construct(
        BookableAreaRepository $bookableAreaRepository,
        BuildingRepository $buildingRepository
    ) {
        $this->bookableAreaRepository = $bookableAreaRepository;
        $this->buildingRepository = $buildingRepository;
    }

    /**
     * Function to instantiate a new BookableArea from given params.
     *
     * @param array $requestData
     *
     * @return BookableArea
     */
    public function createFromRequestData(array $requestData)
    {
        $_bookableArea = new BookableArea();

        $building = $this->buildingRepository->findOneById($requestData['building_id']);

        $_bookableArea->setBuilding($building);
        $_bookableArea->setName($requestData['name']);
        $_bookableArea->setDescription($requestData['description']);
        $_bookableArea->setMaxOccupancy($requestData['maxOccupancy']);
        $_bookableArea->setAdditionalGuestOccupancy($requestData['additionalGuestOccupancy']);
        $_bookableArea->setBookingColor($requestData['bookingColor']);
        $_bookableArea->setSelfBookable(isset($validatedData['selfBookable']) ? true : false);

        return $_bookableArea;
    }
}
