<?php

namespace App\Http\Controllers\Snackspace;

use Carbon\Carbon;
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
        $chart->dataset('Total Debt', 'line', $debt->values())
            ->color('#ff0000');
        $chart->dataset('Total Credit', 'line', $credit->values())
            ->color('#00ff00');

        $dataRecent = collect($this->debtRepository->findBetweeenAuditTimes(Carbon::now()->subYear(), Carbon::now()));
        $dataRecent = $dataRecent->map(function ($debt) {
            return [
                'td' => $debt->getTotalDebt() / 100,
                'tc' => $debt->getTotalCredit() / 100,
                'audit_time' => $debt->getAuditTime()->toDateString(),
            ];
        });

        $debt = $dataRecent->pluck('td', 'audit_time');
        $credit = $dataRecent->pluck('tc', 'audit_time');
        $keys = $debt->keys();

        $chartRecent = new SnackspaceDebtChart;
        $chartRecent->labels($keys);
        $chartRecent->dataset('Total Debt', 'line', $debt->values())
            ->color('#ff0000');
        $chartRecent->dataset('Total Credit', 'line', $credit->values())
            ->color('#00ff00');

        return view('snackspace.debt_graph')
            ->with('chartAll', $chart)
            ->with('recent', $chartRecent);
    }
}
