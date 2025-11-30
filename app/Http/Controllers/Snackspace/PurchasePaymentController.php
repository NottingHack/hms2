<?php

namespace App\Http\Controllers\Snackspace;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use HMS\Repositories\Snackspace\TransactionRepository;
use HMS\Views\Payment;
use Illuminate\Http\Request;

class PurchasePaymentController extends Controller
{
    /**
     * @var TransactionRepository
     */
    protected $transactionRepository;

    /**
     * Create a new controller instance.
     *
     * @param TransactionRepository $transactionRepository
     */
    public function __construct(
        TransactionRepository $transactionRepository
    ) {
        $this->transactionRepository = $transactionRepository;

        $this->middleware('feature:snackspace');
        $this->middleware('can:snackspace.transaction.view.all')->only(['paymentReport']);
    }

    /**
     * View payment report of a period.
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function paymentReport(Request $request)
    {
        $validatedData = $request->validate([
            'startDate' => 'required_with:endDate|date_format:Y-m-d',
            'endDate' => 'required_with:startDate|date_format:Y-m-d',
        ]);

        if (array_key_exists('startDate', $validatedData)) {
            $startDate = new Carbon($validatedData['startDate']);
            $startDate->startOfDay();
            $endDate = new Carbon($validatedData['endDate']);
            $endDate->endOfDay();
        } else {
            $startDate = Carbon::now()->subMonth()->startOfMonth();
            $endDate = $startDate->copy()->endOfMonth();
        }

        // Values direct from the Transaction tale
        $report = $this->transactionRepository->reportPaymnetsBetween($startDate, $endDate);

        $cashPayments = $report->get('CASHPAYMENT', 0);
        $cardPayments = $report->get('ONLINEPAYMENT', 0);
        $bankPayments = $report->get('BANKPAYMENT', 0);
        $manualCredits = $report->get('MANUAL', 0);
        $totalPayments = $cashPayments + $cardPayments + $bankPayments + $manualCredits;

        // values from the View
        $forVendPurchases = Payment::whereBetween('transaction_datetime', [$startDate, $endDate])->sum('for_vend');
        $forToolPurchases = Payment::whereBetween('transaction_datetime', [$startDate, $endDate])->sum('for_tool');
        $forBoxPurchases = Payment::whereBetween('transaction_datetime', [$startDate, $endDate])->sum('for_box');
        $forHeatPurchases = Payment::whereBetween('transaction_datetime', [$startDate, $endDate])->sum('for_heat');
        $forOtherPurchases = Payment::whereBetween('transaction_datetime', [$startDate, $endDate])->sum('for_other');
        // dump($forVendPurchases + $forToolPurchases + $forBoxPurchases + $forOtherPurchases);

        return view('snackspace.purchasePayment.payment_report')
            ->with([
                'startDate' => $startDate,
                'endDate' => $endDate,
                'totalPayments' => $totalPayments,
                'cashPayments' => $cashPayments,
                'cardPayments' => $cardPayments,
                'bankPayments' => $bankPayments,
                'manualCredits' => $manualCredits,
                'forVendPurchases' => $forVendPurchases,
                'forToolPurchases' => $forToolPurchases,
                'forBoxPurchases' => $forBoxPurchases,
                'forHeatPurchases' => $forHeatPurchases,
                'forOtherPurchases' => $forOtherPurchases,
            ]);
    }
}
