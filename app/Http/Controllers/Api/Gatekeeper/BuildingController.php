<?php

namespace App\Http\Controllers\Api\Gatekeeper;

use Illuminate\Http\Request;
use HMS\Gatekeeper\BuildingManager;
use App\Http\Controllers\Controller;
use HMS\Entities\Gatekeeper\Building;
use HMS\Repositories\Gatekeeper\BuildingRepository;
use Illuminate\Http\Response as IlluminateResponse;
use App\Http\Resources\Gatekeeper\Building as BuildingResource;

class BuildingController extends Controller
{
    /**
     * @var BuildingRepository
     */
    protected $buildingRepository;

    /**
     * @var BuildingManager
     */
    protected $buildingManager;

    /**
     * Create a new controller instance.
     *
     * @param BuildingRepository $buildingRepository
     * @param BuildingManager $buildingManager
     */
    public function __construct(
        BuildingRepository $buildingRepository,
        BuildingManager $buildingManager
    ) {
        $this->buildingRepository = $buildingRepository;
        $this->buildingManager = $buildingManager;

        $this->middleware('can:gatekeeper.access.manage');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $buildings = $this->buildingRepository->findAll();

        return BuildingResource::collection($buildings);
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
        //
    }

    /**
     * Display the specified resource.
     *
     * @param Building  $building
     *
     * @return \Illuminate\Http\Response
     */
    public function show($building)
    {
        return new BuildingResource($building);
    }

    /**
     * Update Self Booking Max Occupancy for the specified resource in storage.
     *
     * @param \Illuminate\Http\Request  $request
     * @param Building  $building
     *
     * @return \Illuminate\Http\Response
     */
    public function updateOccupancy(Request $request, Building $building)
    {
        $validatedData = $request->validate([
            'selfBookMaxOccupancy' => 'required|integer|min:1',
        ]);

        $response = $this->buildingManager->updateOccupancy($building, $validatedData['selfBookMaxOccupancy']);

        if (is_string($response)) {
            // response is some sort of error
            return response()->json($response, IlluminateResponse::HTTP_UNPROCESSABLE_ENTITY);
        } else {
            // response is the updated building
            return (new BuildingResource($response))
                ->response()
                ->setStatusCode(IlluminateResponse::HTTP_OK);
        }
    }

    /**
     * Update Access State for the specified resource in storage.
     *
     * @param \Illuminate\Http\Request  $request
     * @param Building  $building
     *
     * @return \Illuminate\Http\Response
     */
    public function updateAccessState(Request $request, Building $building)
    {
        $validatedData = $request->validate([
            'accessState' => 'required|string',
        ]);

        $response = $this->buildingManager->updateAccessState($building, $validatedData['accessState']);

        if (is_string($response)) {
            // response is some sort of error
            return response()->json($response, IlluminateResponse::HTTP_UNPROCESSABLE_ENTITY);
        } else {
            // response is the updated building
            return (new BuildingResource($response))
                ->response()
                ->setStatusCode(IlluminateResponse::HTTP_OK);
        }
    }
}
