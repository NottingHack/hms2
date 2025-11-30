<?php

namespace App\Jobs\Snackspace;

use App\Notifications\Snackspace\CurrentMemberDebt;
use App\Notifications\Snackspace\ExMemberDebt;
use HMS\Entities\Role;
use HMS\Repositories\Banking\BankRepository;
use HMS\Repositories\MetaRepository;
use HMS\Repositories\RoleRepository;
use HMS\Repositories\Snackspace\DebtRepository;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class MemberDebtNotificationJob implements ShouldQueue
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
     * @param RoleRepository $roleRepository
     * @param DebtRepository $debtRepository
     * @param MetaRepository $metaRepository
     * @param BankRepository $bankRepository
     *
     * @return void
     */
    public function handle(
        RoleRepository $roleRepository,
        DebtRepository $debtRepository,
        MetaRepository $metaRepository,
        BankRepository $bankRepository
    ) {
        $bank = $bankRepository->findOneById($metaRepository->getInt('so_bank_id'));
        $accountNo = $bank->getAccountNumber();
        $sortCode = $bank->getSortCode();
        $accountName = $bank->getAccountName();

        $latestDebt = $debtRepository->findLatest();
        $latetsTotalDebt = $latestDebt ? $latestDebt->getTotalDebt() : 0;
        $latetsExDebt = $latestDebt ? $latestDebt->getExDebt() : 0;

        $currentRole = $roleRepository->findOneByName(Role::MEMBER_CURRENT);
        $exRole = $roleRepository->findOneByName(Role::MEMBER_EX);

        $currentMembersInDebt = $currentRole->getUsers()->filter(function ($user, $key) {
            return $user->getProfile()->getBalance() < 0;
        });
        Log::info(
            'MemberDebtNotificationJob: ' . $currentMembersInDebt->count()
            . ' Current members notified about their outstanding balance'
        );

        Notification::send($currentMembersInDebt->toArray(), new CurrentMemberDebt($latetsTotalDebt));

        $exMembersInDebt = $exRole->getUsers()->filter(function ($user, $key) {
            return $user->getProfile()->getBalance() < 0;
        });
        Log::info(
            'MemberDebtNotificationJob: ' . $exMembersInDebt->count()
            . ' Ex members notified about their outstanding balance'
        );

        Notification::send(
            $exMembersInDebt->toArray(),
            new ExMemberDebt($latetsTotalDebt, $latetsExDebt, $accountNo, $sortCode, $accountName)
        );
    }
}
