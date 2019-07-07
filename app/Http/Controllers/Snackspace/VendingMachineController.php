<?php

namespace App\Http\Controllers\Snackspace;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use HMS\Entities\Snackspace\VendingMachine;
use HMS\Entities\Snackspace\VendingLocation;
use HMS\Entities\Snackspace\VendingMachineType;
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
     * @param VendingMachineRepository $vendingMachineRepository
     * @param VendingLocationRepository $vendingLocationRepository
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
        $this->middleware('can:snackspace.vendingMachine.locations.assign')->only(['locations', 'locationAssign']);
    }

    /**
     * Display a listing of the vending machines.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $vendingMachines = $this->vendingMachineRepository->findByType(VendingMachineType::VEND);
        $paymentMachines = $this->vendingMachineRepository->findByType(VendingMachineType::NOTE);

        return view('snackspace.vendingMachine.index')
            ->with('vendingMachines', $vendingMachines)
            ->with('paymentMachines', $paymentMachines);
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
        $locations = $this->vendingLocationRepository->findByVendingMachine($vendingMachine);
        // map for VendingLocation.vue
        $locations = array_map(function ($location) {
            $product = $location->getProduct();
            if (is_null($product)) {
                $productArray = [
                    'id' => -1,
                    'shortDescription' => 'Empty',
                    'price' => null,
                ];
            } else {
                $productArray = [
                    'id' => $product->getId(),
                    'shortDescription' => $product->getShortDescription(),
                    'price' => money_format('%n', $product->getPrice() / 100),
                ];
            }

            return [
                'id' => $location->getId(),
                'name' => $location->getName(),
                'product' => $productArray,
            ];
        }, $locations);

        $products = $this->productRepository->findAll();
        // map for select two usage
        $products = array_map(function ($product) {
            return [
                'id' => $product->getId(),
                'text' => $product->getShortDescription() . ' (' . money_format('%n', $product->getPrice() / 100) . ')',
            ];
        }, $products);
        array_unshift($products, [
            'id' => -1,
            'text' => 'Empty',
        ]);

        return view('snackspace.vendingMachine.locations')
            ->with('vendingMachine', $vendingMachine)
            ->with('locations', $locations)
            ->with('products', $products);
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
        // TODO: test this
        $validatedData = $request->validate([
            'productId' => 'nullable|exists:HMS\Entities\Snackspace\Product,id',
        ]);

        if ($validatedData['productId'] == -1) {
            $newProduct = null;
        } else {
            $newProduct = $this->productRepository->findOneById($validatedData['productId']);
        }

        $vendingLocation->setProduct($newProduct);
        $this->vendingLocationRepository->save($vendingLocation);
        flash('Location updated')->success();

        return redirect()
            ->route('snackspace.vendingMachine.locations', $vendingMachine->getId());
    }
}
