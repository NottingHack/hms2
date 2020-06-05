<?php

namespace App\Http\Controllers\Api\Gatekeeper;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use HMS\Repositories\UserRepository;
use HMS\Gatekeeper\TemporaryAccessBookingManager;
use HMS\Entities\Gatekeeper\TemporaryAccessBooking;
use HMS\Repositories\Gatekeeper\BuildingRepository;
use Illuminate\Http\Response as IlluminateResponse;
use HMS\Repositories\Gatekeeper\BookableAreaRepository;
use HMS\Repositories\Gatekeeper\TemporaryAccessBookingRepository;
use App\Http\Resources\Gatekeeper\TemporaryAccessBooking as TemporaryAccessBookingResources;

class TemporaryAccessBookingController extends Controller
{
    /**
     * @var TemporaryAccessBookingRepository
     */
    protected $temporaryAccessBookingRepository;

    /**
     * @var TemporaryAccessBookingManager
     */
    protected $temporaryAccessBookingManager;

    /**
     * @var UserRepository
     */
    protected $userRepository;

    /**
     * @var BookableAreaRepository
     */
    protected $bookableAreaRepository;

    /**
     * @var BuildingRepository
     */
    protected $buildingRepository;

    /**
     * Create a new api controller instance.
     *
     * @param TemporaryAccessBookingRepository $temporaryAccessBookingRepository
     * @param TemporaryAccessBookingManager $temporaryAccessBookingManager
     * @param UserRepository $userRepository
     * @param BookableAreaRepository $bookableAreaRepository
     * @param BuildingRepository $buildingRepository
     */
    public function __construct(
        TemporaryAccessBookingRepository $temporaryAccessBookingRepository,
        TemporaryAccessBookingManager $temporaryAccessBookingManager,
        UserRepository $userRepository,
        BookableAreaRepository $bookableAreaRepository,
        BuildingRepository $buildingRepository
    ) {
        $this->temporaryAccessBookingRepository = $temporaryAccessBookingRepository;
        $this->temporaryAccessBookingManager = $temporaryAccessBookingManager;
        $this->userRepository = $userRepository;
        $this->bookableAreaRepository = $bookableAreaRepository;
        $this->buildingRepository = $buildingRepository;

        $this->middleware('canAny:gatekeeper.temporaryAccess.view.self,gatekeeper.temporaryAccess.view.all')
            ->only(['index', 'show']);
        $this->middleware('canAny:gatekeeper.temporaryAccess.grant.self,gatekeeper.temporaryAccess.grant.all')
            ->only(['store', 'update', 'destroy']);
    }

    /**
     * Display a listing of the resource.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $validatedData = $request->validate([
            'start' => 'required|date',
            'end' => 'required|date',
            'building_id' => 'required|exists:HMS\Entities\Gatekeeper\Building,id',
        ]);

        $start = new Carbon($validatedData['start']);
        $end = new Carbon($validatedData['end']);
        $building = $this->buildingRepository->findOneById($validatedData['building_id']);

        $temporaryAccessBookings = $this->temporaryAccessBookingRepository
            ->findBetweenForBuilding($start, $end, $building);

        return TemporaryAccessBookingResources::collection($temporaryAccessBookings);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'start' => 'required|date',
            'end' => 'required|date',
            'user_id' => 'required|exists:HMS\Entities\User,id',
            'bookable_area_id' => 'required|nullable|exists:HMS\Entities\Gatekeeper\BookableArea,id',
            'notes' => 'nullable|string|max:250',
        ]);

        $start = new Carbon($validatedData['start']);
        $end = new Carbon($validatedData['end']);
        $user = $this->userRepository->findOneById($validatedData['user_id']);
        $bookableArea = $this->bookableAreaRepository->findOneById($validatedData['bookable_area_id']);

        $response = $this->temporaryAccessBookingManager->book(
            $start,
            $end,
            $user,
            $bookableArea,
            $validatedData['notes']
        );

        if (is_string($response)) {
            // response is some sort of error
            return response()->json($response, IlluminateResponse::HTTP_UNPROCESSABLE_ENTITY);
        } else {
            // response is the new booking object
            return (new TemporaryAccessBookingResources($response))
                ->response()
                ->setStatusCode(IlluminateResponse::HTTP_CREATED);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param TemporaryAccessBooking $temporaryAccessBooking
     *
     * @return \Illuminate\Http\Response
     */
    public function show(TemporaryAccessBooking $temporaryAccessBooking)
    {
        return new TemporaryAccessBookingResources($response);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param TemporaryAccessBooking $temporaryAccessBooking
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function update(TemporaryAccessBooking $temporaryAccessBooking, Request $request)
    {
        $validatedData = $request->validate([
            'start' => 'required|date',
            'end' => 'required|date',
            'approve' => 'sometimes|boolean',
        ]);

        $start = new Carbon($validatedData['start']);
        $end = new Carbon($validatedData['end']);

        if (isset($validatedData['approve'])) {
            $response = $this->temporaryAccessBookingManager
                ->approve($temporaryAccessBooking, $validatedData['approve']);
        } else {
            $response = $this->temporaryAccessBookingManager
                ->update($temporaryAccessBooking, $start, $end);
        }

        if (is_string($response)) {
            // response is some sort of error
            return response()->json($response, IlluminateResponse::HTTP_UNPROCESSABLE_ENTITY);
        } else {
            // response is the updated booking object
            return (new TemporaryAccessBookingResources($response))
                ->response()
                ->setStatusCode(IlluminateResponse::HTTP_OK);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param TemporaryAccessBooking $temporaryAccessBooking
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(TemporaryAccessBooking $temporaryAccessBooking, Request $request)
    {
        // request may have a reason?
        $validatedData = $request->validate([
            'reason' => 'sometimes|string',
        ]);

        if (isset($validatedData['reason'])) {
            if ($temporaryAccessBooking->isApproved()) {
                // if approved and reason
                $response = $this->temporaryAccessBookingManager->cancelWithReason($temporaryAccessBooking, $validatedData['reason']);
            } else {
                // if not approved and reason
                $response = $this->temporaryAccessBookingManager->reject($temporaryAccessBooking, $validatedData['reason']);
            }
        } else {
            // else no reason just cancel
            $response = $this->temporaryAccessBookingManager->cancel($temporaryAccessBooking);
        }

        if (is_string($response)) {
            // response is some sort of error
            return response()->json($response, IlluminateResponse::HTTP_FORBIDDEN);
        } else {
            // response is empty
            // TODO: return what is now lastestBooking
            return response(null, IlluminateResponse::HTTP_NO_CONTENT);
        }
    }
}
