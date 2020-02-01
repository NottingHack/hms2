<?php

namespace App\Http\Controllers\Snackspace;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;
use HMS\Entities\Snackspace\VendLog;
use Doctrine\Common\Collections\Criteria;
use HMS\Entities\Snackspace\VendingMachine;
use HMS\Entities\Snackspace\VendingLocation;
use HMS\Entities\Snackspace\TransactionState;
use HMS\Entities\Snackspace\VendingMachineType;
use HMS\Repositories\Snackspace\ProductRepository;
use HMS\Repositories\Snackspace\VendLogRepository;
use HMS\Repositories\Snackspace\TransactionRepository;
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
     * @var VendLogRepository
     */
    protected $vendLogRepository;

    /**
     * @var TransactionRepository
     */
    protected $transactionRepository;

    /**
     * Create a new controller instance.
     *
     * @param VendingMachineRepository $vendingMachineRepository
     * @param VendingLocationRepository $vendingLocationRepository
     * @param ProductRepository $productRepository
     * @param VendLogRepository $vendLogRepository
     * @param TransactionRepository $transactionRepository
     */
    public function __construct(
        VendingMachineRepository $vendingMachineRepository,
        VendingLocationRepository $vendingLocationRepository,
        ProductRepository $productRepository,
        VendLogRepository $vendLogRepository,
        TransactionRepository $transactionRepository
    ) {
        $this->vendingMachineRepository = $vendingMachineRepository;
        $this->vendingLocationRepository = $vendingLocationRepository;
        $this->productRepository = $productRepository;
        $this->vendLogRepository = $vendLogRepository;
        $this->transactionRepository = $transactionRepository;

        $this->middleware('can:snackspace.vendingMachine.view')->only(['index', 'show']);
        $this->middleware('can:snackspace.vendingMachine.edit')->only(['edit', 'update']);
        $this->middleware('can:snackspace.vendingMachine.locations.assign')->only(['locations', 'locationAssign']);
        $this->middleware('can:snackspace.vendingMachine.reconcile')->only(['showJames']);
        $this->middleware('can:snackspace.transaction.view.all')->only(['showLogs']);
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

    /**
     * Show vending machine logs.
     *
     * @param VendingMachine $vendingMachine
     *
     * @return \Illuminate\Http\Response
     */
    public function showLogs(VendingMachine $vendingMachine)
    {
        $vendLogs = $this->vendLogRepository->paginateByVendingMachine($vendingMachine);

        return view('snackspace.vendingMachine.show_logs')
            ->with('vendingMachine', $vendingMachine)
            ->with('vendLogs', $vendLogs);
    }

    /**
     * Show vending machine jams.
     *
     * @param VendingMachine $vendingMachine
     *
     * @return \Illuminate\Http\Response
     */
    public function showJams(VendingMachine $vendingMachine)
    {
        $vendLogs = $this->vendLogRepository->paginatePeningByVendingMachine($vendingMachine);

        return view('snackspace.vendingMachine.show_jams')
            ->with('vendingMachine', $vendingMachine)
            ->with('vendLogs', $vendLogs);
    }

    /**
     * Reconcile a jammed transaction.
     *
     * @param Request $request
     * @param VendingMachine $vendingMachine
     * @param VendLog $vendLog
     *
     * @return \Illuminate\Http\Response
     */
    public function reconcile(Request $request, VendingMachine $vendingMachine, VendLog $vendLog)
    {
        $validatedData = $request->validate([
            'action' => [
                'required',
                Rule::in(['complete', 'abort']),
            ],
        ]);

        if ($validatedData['action'] == 'complete') {
            // replicate steps from sp_vend_success
            if ($vendLog->getVendingMachine()->getType() == VendingMachineType::NOTE) {
                // Payment made using note acceptor
                $description = 'Cash payment - £' . number_format(abs($vendLog->getAmountScaled() / 100), 2);

                switch ($vendLog->getAmountScaled()) {
                    case -500:
                        $position = 'CHANNEL1';
                        break;

                    case -1000:
                        $position = 'CHANNEL2';
                        break;

                    case -2000:
                        $position = 'CHANNEL3';
                        break;

                    default:
                        $position = 'CHANNEL0';
                        break;
                }
                $vendLog->setPosition($position);
            } else {
                // vending machine
                $criteria = Criteria::create();
                $criteria->where($criteria->expr()->eq('encoding', $vendLog->getPosition()));
                $vendingLocations = $vendLog->getVendingMachine()->getVendingLocations()->matching($criteria);

                if ($vendingLocations->isEmpty()) {
                    // we have no idea what the location and product was
                    $location = 'Unknown';
                    $prodedc = 'Unknown item';
                } else {
                    $vendingLocation = $vendingLocations->first();
                    $location = $vendingLocation->getName();
                    $product = $vendingLocation->getProduct();
                    $prodedc = $product->getShortDescription() ?? 'Unknown item';

                    $vendLog->getTransaction()->setProduct($product);
                }

                $description = '[' . $prodedc . '] vended from location [' . $location . '].';
            }

            $vendLog->getTransaction()->setStatus(TransactionState::COMPLETE);
            $vendLog->getTransaction()->setDescription($description);
            $vendLog->setSuccessTime(Carbon::now());
            $this->vendLogRepository->saveAndUpdateBalance($vendLog);

            flash('Transaction completed')->success();
        } elseif ($validatedData['action'] == 'abort') {
            // replicate steps from sp_vend_failure
            $vendLog->getTransaction()->setStatus(TransactionState::ABORTED);
            $vendLog->getTransaction()->setDescription('Vend Failed (HMS abort)');
            $vendLog->setFailedTime(Carbon::now());
            $this->vendLogRepository->save($vendLog);

            flash('Transaction aborted')->success();
        }

        return redirect()->route('snackspace.vending-machines.logs.jams', $vendingMachine->getId());
    }
}
