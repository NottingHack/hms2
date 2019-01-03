<?php

namespace App\Http\Controllers\Api\Tools;

use Carbon\Carbon;
use HMS\Entities\Tools\Tool;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use HMS\Repositories\Tools\BookingRepository;

class BookingController extends Controller
{
    protected $bookingsRepository;

    public function __construct(BookingRepository $bookingsRepository)
    {
        $this->bookingsRepository = $bookingsRepository;
    }

    protected function mapBookings($booking)
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
     * @return \Illuminate\Http\Response
     */
    public function index(Tool $tool, Request $request)
    {
        $start = new Carbon($request->start);
        $end = new Carbon($request->end);

        $bookings = $this->bookingsRepository->findByToolBetween($tool, $start, $end);
        $mappedBookings = array_map([$this, 'mapBookings'], $bookings);

        return response()->json($mappedBookings);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
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
