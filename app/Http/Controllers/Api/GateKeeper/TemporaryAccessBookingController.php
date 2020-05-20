<?php

namespace App\Http\Controllers\Api\GateKeeper;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use HMS\Repositories\UserRepository;
use HMS\GateKeeper\TemporaryAccessBookingManager;
use HMS\Entities\GateKeeper\TemporaryAccessBooking;
use Illuminate\Http\Response as IlluminateResponse;
use HMS\Repositories\GateKeeper\TemporaryAccessBookingRepository;
use App\Http\Resources\GateKeeper\TemporaryAccessBooking as TemporaryAccessBookingResources;

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
     * Create a new api controller instance.
     *
     * @param TemporaryAccessBookingRepository $temporaryAccessBookingRepository
     * @param TemporaryAccessBookingManager $temporaryAccessBookingManager
     * @param UserRepository $userRepository
     */
    public function __construct(
        TemporaryAccessBookingRepository $temporaryAccessBookingRepository,
        TemporaryAccessBookingManager $temporaryAccessBookingManager,
        UserRepository $userRepository
    ) {
        $this->temporaryAccessBookingRepository = $temporaryAccessBookingRepository;
        $this->temporaryAccessBookingManager = $temporaryAccessBookingManager;
        $this->userRepository = $userRepository;

        $this->middleware('can:gatekeeper.temporaryAccess.grant');
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
        $this->validate($request, [
            'start' => 'required|date',
            'end' => 'required|date',
        ]);

        $start = new Carbon($request->start);
        $end = new Carbon($request->end);

        $temporaryAccessBookings = $this->temporaryAccessBookingRepository->findBetween($start, $end);

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
            'color' => 'nullable|string|max:50',
            'notes' => 'nullable|string|max:250',
        ]);

        $start = new Carbon($validatedData['start']);
        $end = new Carbon($validatedData['end']);
        $user = $this->userRepository->findOneById($validatedData['user_id']);

        $response = $this->temporaryAccessBookingManager->book(
            $start,
            $end,
            $user,
            $validatedData['color'],
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
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
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
        ]);

        $start = new Carbon($validatedData['start']);
        $end = new Carbon($validatedData['end']);

        $response = $this->temporaryAccessBookingManager->update($temporaryAccessBooking, $start, $end);

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
    public function destroy(TemporaryAccessBooking $temporaryAccessBooking)
    {
        $response = $this->temporaryAccessBookingManager->cancel($temporaryAccessBooking);

        if (is_string($response)) {
            // response is some sort of error
            return response()->json($response, IlluminateResponse::HTTP_FORBIDDEN);
        } else {
            // response is empty
            return response(null, IlluminateResponse::HTTP_NO_CONTENT);
        }
    }
}
