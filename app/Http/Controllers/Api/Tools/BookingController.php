<?php

namespace App\Http\Controllers\Api\Tools;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use HMS\Entities\Tools\Booking;
use HMS\Entities\Tools\BookingType;
use HMS\Entities\Tools\Tool;
use HMS\Repositories\Tools\BookingRepository;
use HMS\Tools\BookingManager;
use Illuminate\Http\Request;
use Illuminate\Http\Response as IlluminateResponse;
use Illuminate\Validation\Rule;

class BookingController extends Controller
{
    /**
     * @var BookingRepository
     */
    protected $bookingRepository;

    /**
     * @var BookingManager
     */
    protected $bookingManager;

    /**
     * Create a new api controller instance.
     *
     * @param BookingRepository $bookingRepository
     * @param BookingManager $bookingManager
     */
    public function __construct(BookingRepository $bookingRepository, BookingManager $bookingManager)
    {
        $this->bookingRepository = $bookingRepository;
        $this->bookingManager = $bookingManager;

        $this->middleware('feature:tools');
        $this->middleware('can:tools.view')->only(['index']);
        $this->middleware('can:tools.book')->only(['store', 'show',  'update', 'destroy']);
    }

    /**
     * Display a listing of the resource.
     *
     * @param Tool $tool
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Tool $tool, Request $request)
    {
        $this->validate($request, [
            'start' => 'required|date',
            'end' => 'required|date',
        ]);

        $start = new Carbon($request->start);
        $end = new Carbon($request->end);

        $bookings = $this->bookingRepository->findByToolBetween($tool, $start, $end);

        return response()->json($bookings);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Tool $tool
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Tool $tool, Request $request)
    {
        $this->validate($request, [
            'start' => 'required|date',
            'end' => 'required|date',
            'type' => [
                'required',
                Rule::in([BookingType::NORMAL, BookingType::INDUCTION, BookingType::MAINTENANCE]),
            ],
        ]);

        $start = new Carbon($request->start);
        $end = new Carbon($request->end);

        switch ($request->type) {
            case BookingType::NORMAL:
                $response = $this->bookingManager->bookNormal($tool, $start, $end);
                break;
            case BookingType::INDUCTION:
                $response = $this->bookingManager->bookInduction($tool, $start, $end);
                break;
            case BookingType::MAINTENANCE:
                $response = $this->bookingManager->bookMaintenance($tool, $start, $end);
                break;
        }

        if (is_string($response)) {
            // response is some sort of error
            return response()->json($response, IlluminateResponse::HTTP_UNPROCESSABLE_ENTITY);
        } else {
            // response is the new booking object
            return response()->json($response, IlluminateResponse::HTTP_CREATED);
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
     * @param Tool $tool
     * @param Booking $booking
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Tool $tool, Booking $booking, Request $request)
    {
        $this->validate($request, [
            'start' => 'required|date',
            'end' => 'required|date',
        ]);

        $start = new Carbon($request->start);
        $end = new Carbon($request->end);

        $response = $this->bookingManager->update($tool, $booking, $start, $end);

        if (is_string($response)) {
            // response is some sort of error
            return response()->json($response, IlluminateResponse::HTTP_UNPROCESSABLE_ENTITY);
        } else {
            // response is the new booking object
            return response()->json($response, IlluminateResponse::HTTP_OK);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Tool $tool
     * @param Booking $booking
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(Tool $tool, Booking $booking)
    {
        // check tool and booking id match?
        if ($tool->getId() != $booking->getTool()->getId()) {
            return response()->json('This booking is not for this tool.', IlluminateResponse::HTTP_FORBIDDEN); // 422
        }

        $response = $this->bookingManager->cancel($booking);

        if (is_string($response)) {
            // response is some sort of error
            return response()->json($response, IlluminateResponse::HTTP_FORBIDDEN);
        } else {
            // response is empty
            return response(null, IlluminateResponse::HTTP_NO_CONTENT);
        }
    }
}
