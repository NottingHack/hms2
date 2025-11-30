<?php

namespace App\Http\Controllers\Tools;

use App\Http\Controllers\Controller;
use HMS\Entities\Tools\Tool;
use HMS\Repositories\Tools\BookingRepository;
use HMS\Tools\BookingManager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
     * Create a new controller instance.
     *
     * @param BookingRepository $bookingRepository
     * @param BookingManager $bookingManager
     */
    public function __construct(
        BookingRepository $bookingRepository,
        BookingManager $bookingManager
    ) {
        $this->bookingRepository = $bookingRepository;
        $this->bookingManager = $bookingManager;

        $this->middleware('feature:tools');
        $this->middleware('can:tools.view')->only(['index']);
    }

    /**
     * Display a listing of the resource.
     *
     * @param Tool $tool
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Tool $tool)
    {
        $user = Auth::user();
        $bookingsThisWeek = $this->bookingRepository->findByToolForThisWeek($tool);

        return view('tools.booking.index')
            ->with('tool', $tool)
            ->with('userCanBook', $this->bookingManager->canUserBookTool($user, $tool))
            ->with('bookingsThisWeek', $bookingsThisWeek);
    }
}
