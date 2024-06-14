<?php

namespace App\Http\Controllers\Instrumentation;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use HMS\Entities\Instrumentation\ElectricReading;
use HMS\Repositories\Instrumentation\ElectricMeterRepository;
use HMS\Repositories\Instrumentation\ElectricReadingRepository;
use Illuminate\Http\Request;

class ElectricController extends Controller
{
    protected $electricMeterRepository;

    protected $electricReadingRepository;

    public function __construct(
        ElectricMeterRepository $electricMeterRepository,
        ElectricReadingRepository $electricReadingRepository
    ) {
        $this->electricMeterRepository = $electricMeterRepository;

        $this->middleware('can:instrumentation.electric.addReading')->only(['store']);
        $this->electricReadingRepository = $electricReadingRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $meters = $this->electricMeterRepository->findAll();

        $meterOptions = collect($meters)->map(function ($meter) {
            $lastReading = $this->electricReadingRepository->findLatestReadingForMeter($meter);
            $lastReadingValue = $lastReading ? ', Last reading: ' . $lastReading->getReading() : ', No readings yet';

            return [
                'id' => $meter->getId(),
                'text' => $meter->getName() . $lastReadingValue,
            ];
        })->toArray();

        // readings are normally on the 16th of the month
        $date = Carbon::now();
        if ($date->day < 16) {
            $date->subMonthNoOverflow();
        }
        $date->day = 16;

        $readingsChart = $this->electricReadingRepository->chartReadingsForMeters($meters);

        return view('instrumentation.electric.index')
            ->with('readingsChart', $readingsChart)
            ->with('meterOptions', $meterOptions)
            ->with('readingDate', $date->toDateString());
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
            'meter' => 'required|exists:HMS\Entities\Instrumentation\ElectricMeter,id',
            'reading' => 'required|integer',
            'date' => 'required|date',
        ]);

        $reading = new ElectricReading;

        $meter = $this->electricMeterRepository->findOneById($validatedData['meter']);

        $reading->setMeter($meter);
        $reading->setDate(new Carbon($validatedData['date']));
        $reading->setReading($validatedData['reading']);

        $this->electricReadingRepository->save($reading);

        flash('Reading saved')->success();

        return redirect()->route('instrumentation.electric.index');
    }
}
