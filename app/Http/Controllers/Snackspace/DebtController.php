<?php

namespace App\Http\Controllers\Snackspace;

use App\Charts\SnackspaceDebtChart;
use App\Http\Controllers\Controller;
use HMS\Repositories\Snackspace\DebtRepository;

class DebtController extends Controller
{
    /**
     * @var DebtRepository
     */
    protected $debtRepository;

    /**
     * Create a new controller instance.
     *
     * @param DebtRepository $debtRepository
     */
    public function __construct(DebtRepository $debtRepository)
    {
        $this->debtRepository = $debtRepository;

        $this->middleware('feature:snackspace');
        $this->middleware('can:snackspace.debt.view');
    }

    /**
     * View the current debt grpahs.
     *
     * @return \Illuminate\Http\Response
     */
    public function debtGraph()
    {
        $data = collect($this->debtRepository->findAll());
        $data = $data->map(function ($debt) {
            return [
                'td' => $debt->getTotalDebt() / 100,
                'tc' => $debt->getTotalCredit() / 100,
                'audit_time' => $debt->getAuditTime()->toDateString(),
            ];
        });

        $debt = $data->pluck('td', 'audit_time');
        $credit = $data->pluck('tc', 'audit_time');
        $keys = $debt->keys();

        $chart = new SnackspaceDebtChart;
        $chart->labels($keys);
        $chart->dataset('Total Debt', 'area', $debt->values())
            ->color('#ff0000');
        $chart->dataset('Total Credit', 'area', $credit->values())
            ->color('#00ff00');

        return view('snackspace.debt_graph')
            ->with('chartAll', $chart);
    }
}
