<?php

namespace App\Http\Controllers\Snackspace;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use HMS\Entities\Snackspace\VendingMachine;
use HMS\Entities\Snackspace\VendingLocation;
use HMS\Repositories\Snackspace\ProductRepository;
use HMS\Repositories\Snackspace\VendingMachineRepository;
use HMS\Repositories\Snackspace\VendingLocationRepository;

class VendingMachineController extends Controller
{
    /**
     * @var VendingMachineRepository
     */
    protected $vendingMachineRepository;

    /**
     * @var VendingLocationRepository
     */
    protected $vendingLocationRepository;

    /**
     * @var ProductRepository
     */
    protected $productRepository;

    /**
     * Create a new controller instance.
     *
     * @param VendingMachineRepository $vendingMachineRepository,
     * @param VendingLocationRepository $vendingLocationRepository,
     * @param ProductRepository $productRepository
     */
    public function __construct(
        VendingMachineRepository $vendingMachineRepository,
        VendingLocationRepository $vendingLocationRepository,
        ProductRepository $productRepository
    ) {
        $this->vendingMachineRepository = $vendingMachineRepository;
        $this->vendingLocationRepository = $vendingLocationRepository;
        $this->productRepository = $productRepository;

        $this->middleware('can:snackspace.vendingMachine.view')->only(['index']);
        $this->middleware('can:snackspace.vendingMachine.edit')->only(['edit', 'update']);
        $this->middleware('can:snackspace.vendingMachine.productSetup')->only(['locations', 'locationAssign']);
    }

    /**
     * Display a listing of the vending machines.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Display the specified vending machine.
     *
     * @param \App\Snackspace\VendingMachine $vendingMachine
     *
     * @return \Illuminate\Http\Response
     */
    public function show(VendingMachine $vendingMachine)
    {
        //
    }

    /**
     * Show the form for editing the specified vending machine.
     *
     * @param \App\Snackspace\VendingMachine $vendingMachine
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(VendingMachine $vendingMachine)
    {
        //
    }

    /**
     * Update the specified vending machine in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Snackspace\VendingMachine $vendingMachine
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, VendingMachine $vendingMachine)
    {
        //
    }

    /**
     * Display a listing of the vending machine locations.
     *
     * @param \App\Snackspace\VendingMachine $vendingMachine
     *
     * @return \Illuminate\Http\Response
     */
    public function locations(VendingMachine $vendingMachine)
    {
        //
    }

    /**
     * Update the specified vending machines location in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Snackspace\VendingMachine $vendingMachine
     * @param \App\Snackspace\VendingLocation $vendingLocation
     *
     * @return \Illuminate\Http\Response
     */
    public function locationAssign(Request $request, VendingMachine $vendingMachine, VendingLocation $vendingLocation)
    {
        dump($request->all());
    }
}
