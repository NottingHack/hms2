<?php

namespace App\Jobs\Banking;

use App\Events\Banking\ExMemberPaymentUnderMinimum;
use App\Events\Banking\MembershipPaymentMinimumWarning;
use App\Events\Banking\MembershipPaymentWarning;
use App\Events\Banking\NewMembershipPaidFor;
use App\Events\Banking\NewMembershipPaidUnderMinimum;
use App\Events\Banking\NonPaymentOfMembership;
use App\Events\Banking\NonPaymentOfMinimumMembership;
use App\Events\Banking\ReinstatementOfMembershipPayment;
use App\HMS\Views\LowLastPaymentAmount;
use App\Notifications\Banking\AuditIssues;
use Carbon\Carbon;
use Carbon\CarbonInterval;
use HMS\Entities\Banking\MembershipStatusNotification;
use HMS\Entities\Role;
use HMS\Repositories\Banking\BankTransactionRepository;
use HMS\Repositories\Banking\MembershipStatusNotificationRepository;
use HMS\Repositories\MetaRepository;
use HMS\Repositories\RoleRepository;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class MembershipAuditJob implements ShouldQueue
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
     * @param BankTransactionRepository              $bankTransactionRepository
     * @param MembershipStatusNotificationRepository $membershipStatusNotificationRepository
     * @param MetaRepository                         $metaRepository
     * @param RoleRepository                         $roleRepository
     *
     * @return void
     */
    public function handle(
        BankTransactionRepository $bankTransactionRepository,
        MembershipStatusNotificationRepository $membershipStatusNotificationRepository,
        MetaRepository $metaRepository,
        RoleRepository $roleRepository
    ) {
        $minimumAmount = $metaRepository->getInt('membership_minimum_amount', 500);

        // get the latest transaction date for all accounts, store in $latestTransactionForAccounts
        $lastPaymentAmounts = LowLastPaymentAmount::all();
        /*
            Results data format
            [
                account_id => LowLastPaymentAmount,
                ...
            ]
         */
        $latestTransactionForAccounts = $lastPaymentAmounts->keyBy('account_id');

        // need to grab a list of all members with current notifications
        $outstandingNotifications = collect(
            $membershipStatusNotificationRepository->findOutstandingNotifications()
        );
        /*
            Results data format
            [
                user_id => MembershipStatusNotification,
                ...
            ]
        */
        $memberIdsForCurrentNonPaymentNotifications = $outstandingNotifications
          ->filter(fn (MembershipStatusNotification $membershipStatusNotification) => $membershipStatusNotification->isForNonPayment())
          ->keyBy(fn (MembershipStatusNotification $membershipStatusNotification) => $membershipStatusNotification->getUser()->getId());

        $memberIdsForCurrentUnderPaymentNotifications = $outstandingNotifications
          ->filter(fn (MembershipStatusNotification $membershipStatusNotification) => $membershipStatusNotification->isForUnderMinimumPayment())
          ->keyBy(fn (MembershipStatusNotification $membershipStatusNotification) => $membershipStatusNotification->getUser()->getId());

        // grab the users in each of the various role states we need to audit
        $awaitingMembers = $roleRepository->findOneByName(Role::MEMBER_PAYMENT)->getUsers();
        $currentMembers = $roleRepository->findOneByName(Role::MEMBER_CURRENT)->getUsers();
        $youngMembers = $roleRepository->findOneByName(Role::MEMBER_YOUNG)->getUsers();
        $exMembers = $roleRepository->findOneByName(Role::MEMBER_EX)->getUsers();

        // now we have the data we need from the DB setup some working vars
        $approveUsers = [];
        $awaitingUsersUnderMinimum = [];
        $warnUsersNotPaid = [];
        $warnUsersMinimumAmount = [];
        $revokeUsersNotPaid = [];
        $revokeUsersMinimumAmount = [];
        $reinstateUsers = [];
        $exUsersUnderMinimum = [];
        $ohCrapUsers = [];
        $notificationRevokeUsers = [];
        $notificationPaymentUsers = [];
        $notificationUnderPaymentUsers = [];

        // this will be the server time the we run, might need to shift time portion to end of the day 23:59
        $dateNow = Carbon::now();
        $dateNow->setTime(0, 0, 0);
        $warnDate = clone $dateNow;
        $warnDate->sub(
            CarbonInterval::instance(
                new \DateInterval($metaRepository->get('audit_warn_interval', 'P1M14D'))
            )
        );
        $revokeDate = clone $dateNow;
        $revokeDate->sub(
            CarbonInterval::instance(
                new \DateInterval($metaRepository->get('audit_revoke_interval', 'P2M'))
            )
        );
        $notificationRevokeDate = clone $dateNow;
        $notificationRevokeDate->subDays(14);

        foreach ($awaitingMembers as $user) {
            if (isset($latestTransactionForAccounts[$user->getAccount()->getId()])) {
                $transactionDate = $latestTransactionForAccounts[$user->getAccount()->getId()]->last_payment_date;
            } else {
                $transactionDate = null;
            }

            if ($transactionDate === null) {
                continue; // not paid us yet nothing to do here
            } elseif ($transactionDate > $revokeDate) { // transaction date is newer than revoke date
                if ($latestTransactionForAccounts[$user->getAccount()->getId()]->amount_joint_adjusted < $minimumAmount) {
                    // have made a payment but it is below the limit
                    $awaitingUsersUnderMinimum[] = $user;
                } else {
                    // approve member
                    $approveUsers[] = $user;
                }
            } else { // transaction date is older than revoke date
                // why have they not yet been approved yet tell the admins
                $ohCrapUsers[] = $user;
            }
        }

        foreach ($currentMembers as $user) {
            if (isset($latestTransactionForAccounts[$user->getAccount()->getId()])) {
                $transactionDate = $latestTransactionForAccounts[$user->getAccount()->getId()]->last_payment_date;
            } else {
                $transactionDate = null;
            }

            if ($transactionDate === null) {
                // current member that has never paid us?
                // tell the admins
                $ohCrapUsers[] = $user;
            } elseif ($transactionDate < $revokeDate) { // transaction date is older than revoke date
                // make ex member
                $revokeUsersNotPaid[] = $user;
                // clear notification if needed
                $notificationRevokeUsers[] = $user;
            } elseif ($transactionDate < $warnDate) { // transaction date is older than warning date
                // if not already warned
                if ($memberIdsForCurrentNonPaymentNotifications->keys()->doesntContain($user->getId())) {
                    // warn membership may be terminated if we don't see one soon
                    $warnUsersNotPaid[] = $user;
                }
            } elseif ($latestTransactionForAccounts[$user->getAccount()->getId()]->amount_joint_adjusted < $minimumAmount) {
                // date diff should be less than 1.5 months
                // but have not paid enough
                if ($memberIdsForCurrentUnderPaymentNotifications->keys()->doesntContain($user->getId())) {
                    // first time processing at under minimum so
                    // warn them about under payment
                    $warnUsersMinimumAmount[] = $user;
                } else { // ? not sure
                    // latest tx date is good but amount is too low, we have sent them a warning
                    // grab that notification
                    $membershipStatusNotification = $memberIdsForCurrentUnderPaymentNotifications[$user->getId()];

                    // is the warned transaction Date now < revokeDate
                    if ($membershipStatusNotification->getBankTransaction()->getTransactionDate() < $revokeDate
                        || $membershipStatusNotification->getTimeIssued() < $notificationRevokeDate) {
                        //  yes time to revoke them
                        $revokeUsersMinimumAmount[] = $user;
                    }
                }
            } else {
                // date diff should be less than 1.5 months
                // and have paid at least the minimum
                // clear any out standing warnings
                if ($memberIdsForCurrentNonPaymentNotifications->keys()->contains($user->getId())) {
                    $notificationPaymentUsers[] = $user;
                }

                if ($memberIdsForCurrentUnderPaymentNotifications->keys()->contains($user->getId())) {
                    $notificationUnderPaymentUsers[] = $user;
                }
            }
        }

        foreach ($youngMembers as $user) {
            if (isset($latestTransactionForAccounts[$user->getAccount()->getId()])) {
                $transactionDate = $latestTransactionForAccounts[$user->getAccount()->getId()]->last_payment_date;
            } else {
                $transactionDate = null;
            }

            if ($transactionDate === null) {
                // current member that has never paid us?
                // tell the admins
                $ohCrapUsers[] = $user;
            } elseif ($transactionDate < $revokeDate) { // transaction date is older than revoke date
                // make ex member
                $revokeUsersNotPaid[] = $user;
                // clear notification if needed
                $notificationRevokeUsers[] = $user;
            } elseif ($transactionDate < $warnDate) { // transaction date is older than warning date
                // if not already warned
                if ($memberIdsForCurrentNonPaymentNotifications->keys()->doesntContain($user->getId())) {
                    // warn membership may be terminated if we don't see one soon
                    $warnUsersNotPaid[] = $user;
                }
            } elseif ($latestTransactionForAccounts[$user->getAccount()->getId()]->amount_joint_adjusted < $minimumAmount) {
                // date diff should be less than 1.5 months
                // but have not paid enough
                if ($memberIdsForCurrentUnderPaymentNotifications->keys()->doesntContain($user->getId())) {
                    // first time processing at under minimum so
                    // warn them about under payment
                    $warnUsersMinimumAmount[] = $user;
                } else { // ? not sure
                    // latest tx date is good but amount is too low, we have sent them a warning
                    // grab that notification
                    $membershipStatusNotification = $memberIdsForCurrentUnderPaymentNotifications[$user->getId()];

                    // is the warned transaction Date now < revokeDate
                    if ($membershipStatusNotification->getBankTransaction()->getTransactionDate() < $revokeDate
                        || $membershipStatusNotification->getTimeIssued() < $notificationRevokeDate) {
                        //  yes time to revoke them
                        $revokeUsersMinimumAmount[] = $user;
                    }
                }
            } else {
                // date diff should be less than 1.5 months
                // and have paid at least the minimum
                // clear any out standing warnings
                if ($memberIdsForCurrentNonPaymentNotifications->keys()->contains($user->getId())) {
                    $notificationPaymentUsers[] = $user;
                }

                if ($memberIdsForCurrentUnderPaymentNotifications->keys()->contains($user->getId())) {
                    $notificationUnderPaymentUsers[] = $user;
                }
            }
        }

        foreach ($exMembers as $user) {
            if (isset($latestTransactionForAccounts[$user->getAccount()->getId()])) {
                $transactionDate = $latestTransactionForAccounts[$user->getAccount()->getId()]->last_payment_date;
            } else {
                $transactionDate = null;
            }

            if ($transactionDate > $revokeDate) { // transaction date is newer than revoke date
                if ($latestTransactionForAccounts[$user->getAccount()->getId()]->amount_joint_adjusted >= $minimumAmount) {
                    // paid equal or above the minimumAmount
                    // reinstate member
                    $reinstateUsers[] = $user;

                    continue;
                }

                // but have not paid enough
                // only email if there previous payment was before the revoke date and we have not emailed about the transaction before

                // ordered DESC so first should be latestTransaction second is one we need to check date on
                $accountTransactions = $bankTransactionRepository->paginateByAccount($user->getAccount())->items();

                if (count($accountTransactions) < 2) {
                    // oh crap?
                    continue;
                }

                $latestTransaction = $accountTransactions[0];
                $previousTransaction = $accountTransactions[1];

                $userNotifications = collect($membershipStatusNotificationRepository->findByUser($user));

                if ($userNotifications->contains(
                    function (MembershipStatusNotification $membershipStatusNotification) use ($latestTransaction) {
                        return optional($membershipStatusNotification->getBankTransaction())->getId() == $latestTransaction->getId();
                    }
                )) {
                    // the latestTransaction was associated with a previous MembershipStatusNotification
                    continue;
                }

                if ($previousTransaction->getTransactionDate() < $revokeDate) {
                    // previous transaction was before revokeDate
                    $exUsersUnderMinimum[$user->getId()] = [
                        'user' => $user,
                        'latestTransaction' => $latestTransaction,
                    ];
                }
            }
        }

        // right should now have 8 arrays of Id's to go and process
        // by batching the id's we can send just one email to membership team with tables of members
        // showing different bits of info for different states
        // approve, name, email, pin, joint?
        // warn, name, email, last payment date, ref, last visit date, joint?
        // revoke, name, email, last payment date, ref, last visit date, joint?
        // reinstate, name, email, date they were made ex, last visit date, joint?
        // ohcrap list to software@, member_id

        foreach ($approveUsers as $user) {
            event(new NewMembershipPaidFor($user));
        }

        foreach ($warnUsersNotPaid as $user) {
            event(new MembershipPaymentWarning($user));
        }

        foreach ($revokeUsersNotPaid as $user) {
            event(new NonPaymentOfMembership($user));
        }

        foreach ($reinstateUsers as $user) {
            event(new ReinstatementOfMembershipPayment($user));
        }

        foreach ($awaitingUsersUnderMinimum as $user) {
            event(new NewMembershipPaidUnderMinimum($user));
        }

        foreach ($warnUsersMinimumAmount as $user) {
            event(new MembershipPaymentMinimumWarning($user));
        }

        foreach ($revokeUsersMinimumAmount as $user) {
            event(new NonPaymentOfMinimumMembership($user));
        }

        foreach ($exUsersUnderMinimum as $userId => $details) {
            event(new ExMemberPaymentUnderMinimum($details['user'], $details['latestTransaction']));
        }

        if (count($ohCrapUsers) != 0) {
            $softwareTeamRole = $roleRepository->findOneByName(Role::TEAM_SOFTWARE);
            $softwareTeamRole->notify(new AuditIssues($ohCrapUsers));
        }

        // before sending out team emails clean up the warnings for people that have now paid us
        foreach ($notificationPaymentUsers as $user) {
            $userNotifications = $membershipStatusNotificationRepository
                ->findOutstandingNotificationsByUser($user);

            foreach ($userNotifications as $notification) {
                $notification->clearNotificationsByPayment();
                $membershipStatusNotificationRepository->save($notification);
            }
        }

        foreach ($notificationUnderPaymentUsers as $user) {
            $userNotifications = $membershipStatusNotificationRepository
                ->findOutstandingNotificationsByUser($user);

            foreach ($userNotifications as $notification) {
                $notification->clearNotificationsByPayment();
                $membershipStatusNotificationRepository->save($notification);
            }
        }

        // need to delay the results processing to make sure NewMembershipPaidFor events have been processed and new users have pins, using a job to help with the delay
        AuditResultJob::dispatch(
            $approveUsers,
            $warnUsersNotPaid,
            $revokeUsersNotPaid,
            $reinstateUsers,
            count($notificationPaymentUsers) + count($notificationUnderPaymentUsers),
            $awaitingUsersUnderMinimum,
            $warnUsersMinimumAmount,
            $revokeUsersMinimumAmount,
            collect($exUsersUnderMinimum)->pluck('user'),
        )->delay(now()->addMinutes(1));
    }
}
