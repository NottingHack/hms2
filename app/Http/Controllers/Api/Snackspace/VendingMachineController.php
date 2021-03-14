<?php

namespace App\Http\Controllers\Api\Snackspace;

use App\Http\Controllers\Controller;
use HMS\Entities\Snackspace\VendingMachine;
use HMS\Repositories\Snackspace\ProductRepository;
use HMS\Repositories\Snackspace\VendingLocationRepository;
use Illuminate\Http\Request;
use Illuminate\Http\Response as IlluminateResponse;

class VendingMachineController extends Controller
{
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
     * @param VendingLocationRepository $vendingLocationRepository
     * @param ProductRepository $productRepository
     */
    public function __construct(
        VendingLocationRepository $vendingLocationRepository,
        ProductRepository $productRepository
    ) {
        $this->vendingLocationRepository = $vendingLocationRepository;
        $this->productRepository = $productRepository;

        $this->middleware('feature:snackspace,vending');
        $this->middleware('can:snackspace.vendingMachine.locations.assign')->only(['locationAssign']);
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
    public function locationAssign(Request $request, VendingMachine $vendingMachine)
    {
        $validatedData = $request->validate([
            'vendingLocationId' => 'required|exists:HMS\Entities\Snackspace\VendingLocation,id',
            'productId' => 'required|integer',
        ]);

        $vendingLocation = $this->vendingLocationRepository->findOneById($validatedData['vendingLocationId']);

        if ($validatedData['productId'] == -1) {
            $newProduct = null;
        } else {
            $newProduct = $this->productRepository->findOneById($validatedData['productId']);
        }

        $vendingLocation->setProduct($newProduct);
        $this->vendingLocationRepository->save($vendingLocation);

        $product = $vendingLocation->getProduct();
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

        $vendingLocationMapped = [
            'id' => $vendingLocation->getId(),
            'name' => $vendingLocation->getName(),
            'product' => $productArray,
        ];

        return response()->json($vendingLocationMapped, IlluminateResponse::HTTP_OK);
    }
}
