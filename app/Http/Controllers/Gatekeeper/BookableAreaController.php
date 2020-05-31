<?php

namespace App\Http\Controllers\Gatekeeper;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;
use HMS\Entities\Gatekeeper\BookableArea;
use HMS\Factories\Gatekeeper\BookableAreaFactory;
use HMS\Repositories\Gatekeeper\BuildingRepository;
use HMS\Entities\Gatekeeper\BookableAreaBookingColor;
use HMS\Repositories\Gatekeeper\BookableAreaRepository;

class BookableAreaController extends Controller
{
    /**
     * @var BookableAreaRepository
     */
    protected $bookableAreaRepository;

    /**
     * @var BookableAreaFactory
     */
    protected $bookableAreaFactory;

    /**
     * @var BuildingRepository
     */
    protected $buildingRepository;

    /**
     * Create a new controller instance.
     *
     * @param BookableAreaRepository $bookableAreaRepository
     * @param BookableAreaFactory $bookableAreaFactory
     * @param BuildingRepository $buildingRepository
     */
    public function __construct(
        BookableAreaRepository $bookableAreaRepository,
        BookableAreaFactory $bookableAreaFactory,
        BuildingRepository $buildingRepository
    ) {
        $this->bookableAreaRepository = $bookableAreaRepository;
        $this->bookableAreaFactory = $bookableAreaFactory;
        $this->buildingRepository = $buildingRepository;

        $this->middleware('can:gatekeeper.access.manage');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $bookableAreas = $this->bookableAreaRepository->findAll();

        // sort areas by building to give a nice display
        $formattedAreas = [];
        $buildingAccessStates = [];

        foreach ($bookableAreas as $bookableArea) {
            $building = $bookableArea->getBuilding();
            $buildingName = $building->getName();

            if (! isset($formattedAreas[$buildingName])) {
                $formattedAreas[$buildingName] = [];
            }
            if (! isset($buildingAccessStates[$buildingName])) {
                $buildingAccessStates[$buildingName] = $building->getAccessStateString();
            }

            $formattedAreas[$buildingName][] = $bookableArea;
        }

        $buildingNames = array_keys($formattedAreas);
        foreach ($buildingNames as $buildingName) {
            usort($formattedAreas[$buildingName], function ($a, $b) {
                return strcmp($a->getName(), $b->getName());
            });
        }

        ksort($formattedAreas);

        return view('gatekeeper.bookableAreas.index')
            ->with('bookableAreas', $formattedAreas)
            ->with('buildingAccessStates', $buildingAccessStates);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $buildings = $this->buildingRepository->findAll();
        $buildingOptions = [];
        foreach ($buildings as $building) {
            $buildingOptions[] = [
                'id' => $building->getId(),
                'text' => $building->getName(),
            ];
        }

        return view('gatekeeper.bookableAreas.create')
            ->with('buildingOptions', $buildingOptions);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request  $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'building_id' => 'required|exists:HMS\Entities\Gatekeeper\Building,id',
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'maxOccupancy' => 'required|integer|min:1',
            'additionalGuestOccupancy' => 'required|integer|min:0',
            'bookingColor' => [
                'required',
                Rule::in(array_keys(BookableAreaBookingColor::COLOR_STRINGS)),
            ],
            'selfBookable' => 'sometimes|required',
        ]);

        $bookableArea = $this->bookableAreaFactory->createFromRequestData($validatedData);
        $this->bookableAreaRepository->save($bookableArea);
        flash('Bookable Area \'' . $bookableArea->getName() . '\' created.')->success();

        return redirect()->route('gatekeeper.bookable-area.index', $bookableArea->getId());
    }

    /**
     * Display the specified resource.
     *
     * @param BookableArea $bookableArea
     *
     * @return \Illuminate\Http\Response
     */
    public function show(BookableArea $bookableArea)
    {
        return view('gatekeeper.bookableAreas.show')
            ->with('bookableArea', $bookableArea);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param BookableArea $bookableArea
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(BookableArea $bookableArea)
    {
        return view('gatekeeper.bookableAreas.edit')
            ->with('bookableArea', $bookableArea);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request  $request
     * @param BookableArea $bookableArea
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, BookableArea $bookableArea)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:50',
            'description' => 'required|string',
            'maxOccupancy' => 'required|integer|min:1',
            'additionalGuestOccupancy' => 'required|integer|min:0',
            'bookingColor' => [
                'required',
                Rule::in(array_keys(BookableAreaBookingColor::COLOR_STRINGS)),
            ],
            'selfBookable' => 'sometimes|required',
        ]);

        $bookableArea->setName($validatedData['name']);
        $bookableArea->setDescription($validatedData['description']);
        $bookableArea->setMaxOccupancy($validatedData['maxOccupancy']);
        $bookableArea->setAdditionalGuestOccupancy($validatedData['additionalGuestOccupancy']);
        $bookableArea->setBookingColor($validatedData['bookingColor']);
        $bookableArea->setSelfBookable(isset($validatedData['selfBookable']) ? true : false);

        $this->bookableAreaRepository->save($bookableArea);
        flash('Bookable Area \'' . $bookableArea->getName() . '\' updated.')->success();

        return redirect()->route('gatekeeper.bookable-area.show', $bookableArea->getId());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param BookableArea $bookableArea
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(BookableArea $bookableArea)
    {
        $this->bookableAreaRepository->remove($bookableArea);
        flash('Bookable Area \'' . $bookableArea->getName() . '\' removed.')->success();

        return redirect()->route('gatekeeper.bookable-area.index');
    }
}
