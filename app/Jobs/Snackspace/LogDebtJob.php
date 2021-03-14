<?php

namespace App\Jobs\Snackspace;

use Carbon\Carbon;
use HMS\Entities\Snackspace\Debt;
use HMS\Repositories\ProfileRepository;
use HMS\Repositories\Snackspace\DebtRepository;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class LogDebtJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(DebtRepository $debtRepository, ProfileRepository $profileRepository)
    {
        $debt = new Debt();
        $debt->setAuditTime(Carbon::now());
        $debt->setTotalDebt($profileRepository->totalDebt());
        $debt->setCurrentDebt($profileRepository->totalDebtForCurrentMembers());
        $debt->setExDebt($profileRepository->totalDebtForExMembers());

        $debt->setTotalCredit($profileRepository->totalCredit());
        $debt->setCurrentCredit($profileRepository->totalCreditForCurrentMembers());
        $debt->setExCredit($profileRepository->totalCreditForExMembers());

        $debtRepository->save($debt);

        Log::info(
            'TotalDebt: £' . $debt->getTotalDebt() / 100
            . ', Current: £' . $debt->getCurrentDebt() / 100
            . ', Ex: £' . $debt->getExDebt() / 100
        );
        Log::info(
            'TotalCredit: £' . $debt->getTotalCredit() / 100
            . ', Current: £' . $debt->getCurrentCredit() / 100
            . ', Ex: £' . $debt->getExCredit() / 100
        );
    }
}
