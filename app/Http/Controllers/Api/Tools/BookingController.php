<?php

namespace App\Http\Controllers\Api\Tools;

use Carbon\Carbon;
use HMS\Entities\Tools\Tool;
use Illuminate\Http\Request;
use HMS\Tools\BookingManager;
use HMS\Entities\Tools\Booking;
use Illuminate\Validation\Rule;
use HMS\Entities\Tools\BookingType;
use App\Http\Controllers\Controller;
use HMS\Repositories\Tools\BookingRepository;
use Illuminate\Http\Response as IlluminateResponse;

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
    }

    protected function mapBookings(Booking $booking)
    {
        // TODO: swap out for Fractal
        return [
            'id' => $booking->getId(),
            'start' => $booking->getStart()->toAtomString(),
            'end' => $booking->getEnd()->toAtomString(),
            'title' => $booking->getUser()->getFullName(),
            'className' => 'tool-'.strtolower($booking->getType()),
            'toolId' => $booking->getTool()->getId(),
        ];
    }

    /**
     * Display a listing of the resource.
     *
     * @param Tool $tool
     * @param \Illuminate\Http\Request $request
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
        $mappedBookings = array_map([$this, 'mapBookings'], $bookings);

        return response()->json($mappedBookings);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Tool $tool
     * @param  \Illuminate\Http\Request  $request
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
            return response()->json($this->mapBookings($response), IlluminateResponse::HTTP_CREATED);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
