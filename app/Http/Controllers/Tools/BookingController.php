<?php

namespace App\Http\Controllers\Tools;

use HMS\Entities\Tools\Tool;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use HMS\Repositories\Tools\BookingRepository;

class BookingController extends Controller
{
    /**
     * @var BookingRepository
     */
    protected $bookingRepository;

    /**
     * Create a new controller instance.
     *
     * @param BookingRepository $bookingRepository
     */
    public function __construct(BookingRepository $bookingRepository)
    {
        $this->bookingRepository = $bookingRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @param  Tool $tool
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Tool $tool)
    {
        $bookingsThisWeek = $this->bookingRepository->findByToolForThisWeek($tool);

        return view('tools.booking.index')
            ->with('tool', $tool)
            ->with('bookingsThisWeek', $bookingsThisWeek);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
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
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
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
