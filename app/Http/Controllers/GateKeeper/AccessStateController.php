<?php

namespace App\Http\Controllers\GateKeeper;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use HMS\Repositories\MetaRepository;
use HMS\Repositories\GateKeeper\BuildingRepository;

class AccessStateController extends Controller
{
    /**
     * @var BuildingRepository
     */
    protected $buildingRepository;

    /**
     * @var MetaRepository
     */
    protected $metaRepository;

    /**
     * Create a new controller instance.
     *
     * @param BuildingRepository $buildingRepository
     * @param MetaRepository $metaRepository
     */
    public function __construct(
        BuildingRepository $buildingRepository,
        MetaRepository $metaRepository
    ) {
        $this->buildingRepository = $buildingRepository;
        $this->metaRepository = $metaRepository;

        $this->middleware('can:gatekeeper.access.manage');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('gateKeeper.accessState.index')
            ->with($this->getSettingsFromMeta());
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function edit()
    {
        return view('gateKeeper.accessState.edit')
            ->with($this->getSettingsFromMeta());
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request  $request
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $validatedData = $request->validate([
            'maxLength' => 'required|integer|min:15',
            'maxConcurrentPerUser' => 'required|integer|min:1',
            'maxGuestsPerUser' => 'required|integer|min:0',
            'minPeriodBetweenBookings' => 'required|integer|min:0',
            'bookingInfoText' => 'required|string|max:255',
        ]);

        $this->metaRepository->set('self_book_max_length', $validatedData['maxLength']);
        $this->metaRepository->set('self_book_max_concurrent_per_user', $validatedData['maxConcurrentPerUser']);
        $this->metaRepository->set('self_book_max_guests_per_user', $validatedData['maxGuestsPerUser']);
        $this->metaRepository->set('self_book_min_period_between_bookings', $validatedData['minPeriodBetweenBookings']);
        $this->metaRepository->set('self_book_info_text', $validatedData['bookingInfoText']);

        flash('Self Book Global Settings Updated')->success();

        return redirect()->route('gatekeeper.access-state.index');
    }

    /**
     * Get all the self book setting From Meta.
     *
     * @return array
     */
    protected function getSettingsFromMeta()
    {
        return [
            'maxLength' => $this->metaRepository->get('self_book_max_length'),
            'maxConcurrentPerUser' => $this->metaRepository->get('self_book_max_concurrent_per_user'),
            'maxGuestsPerUser' => $this->metaRepository->get('self_book_max_guests_per_user'),
            'minPeriodBetweenBookings' => $this->metaRepository->get('self_book_min_period_between_bookings'),
            'bookingInfoText' => $this->metaRepository->get('self_book_info_text'),
        ];
    }
}
