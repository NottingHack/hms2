<?php

namespace App\Http\Controllers\Gatekeeper;

use HMS\Entities\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use HMS\Repositories\MetaRepository;
use HMS\Entities\Gatekeeper\Building;
use HMS\Gatekeeper\TemporaryAccessBookingManager;
use App\Jobs\Gatekeeper\UserHasLeftTheBuildingJob;
use HMS\Repositories\Gatekeeper\BuildingRepository;

class AccessController extends Controller
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
     * @var TemporaryAccessBookingManager
     */
    protected $temporaryAccessBookingManager;

    /**
     * Create a new controller instance.
     *
     * @param BuildingRepository $buildingRepository
     * @param MetaRepository $metaRepository
     * @param TemporaryAccessBookingManager $temporaryAccessBookingManager
     */
    public function __construct(
        BuildingRepository $buildingRepository,
        MetaRepository $metaRepository,
        TemporaryAccessBookingManager $temporaryAccessBookingManager
    ) {
        $this->buildingRepository = $buildingRepository;
        $this->metaRepository = $metaRepository;
        $this->temporaryAccessBookingManager = $temporaryAccessBookingManager;

        $this->middleware('can:gatekeeper.access.manage')->only(['index', 'edit', 'update']);
        $this->middleware('can:gatekeeper.temporaryAccess.view.all')->only(['accessCalendar']);
        $this->middleware('can:accessCodes.view')->only(['spaceAccess']);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('gatekeeper.accessState.index')
            ->with($this->temporaryAccessBookingManager->getSelfBookSettings());
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function edit()
    {
        return view('gatekeeper.accessState.edit')
            ->with($this->temporaryAccessBookingManager->getSelfBookSettings());
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
     * Admin view Temporary Access Bookings.
     *
     * @param \Illuminate\Http\Request  $request
     *
     * @return \Illuminate\Http\Response
     */
    public function accessCalendar()
    {
        $buildings = $this->buildingRepository->findAll();

        // build settings for TemporarAccess vue
        $settings = $this->temporaryAccessBookingManager->getTemporaryAccessSettings();

        return view('gatekeeper.temporary_access')
            ->with('buildings', $buildings)
            ->with('settings', $settings);
    }

    /**
     * User view Temporary Access Bookings.
     *
     * @param \Illuminate\Http\Request  $request
     *
     * @return \Illuminate\Http\Response
     */
    public function spaceAccess()
    {
        $buildings = $this->buildingRepository->findAll();

        // build settings for TemporarAccess vue
        $settings = $this->temporaryAccessBookingManager->getTemporaryAccessSettings();
        // fudge down grant from all to self
        if (\Gate::allows('gatekeeper.temporaryAccess.grant.all')) {
            $settings['grant'] = 'SELF';
        }
        if (\Gate::allows('gatekeeper.temporaryAccess.view.all')) {
            $settings['view'] = 'SELF';
        }

        return view('gatekeeper.space_access')
            ->with('buildings', $buildings)
            ->with('settings', $settings);
    }

    public function haveLeft(Building $building, User $user)
    {
        UserHasLeftTheBuildingJob::dispatch($building, $user);

        return view('gatekeeper.have_left');
    }
}
