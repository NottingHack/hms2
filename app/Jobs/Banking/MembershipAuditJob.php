<?php

namespace App\Jobs\Banking;

use Carbon\Carbon;
use HMS\Entities\Role;
use Carbon\CarbonInterval;
use Illuminate\Bus\Queueable;
use HMS\Repositories\MetaRepository;
use HMS\Repositories\RoleRepository;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use App\Notifications\Banking\AuditIssues;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Events\Banking\NewMembershipPaidFor;
use App\Events\Banking\NonPaymentOfMembership;
use App\Events\Banking\MembershipPaymentWarning;
use HMS\Repositories\Banking\BankTransactionRepository;
use App\Events\Banking\ReinstatementOfMembershipPayment;
use HMS\Repositories\Banking\MembershipStatusNotificationRepository;

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
        // get the latest transaction date for all accounts, store in $latestTransactionForAccounts
        $bts = $bankTransactionRepository->findLatestTransactionForAllAccounts();
        /*
            Results data format
            [int] => Carbon
            [account_id] => transaction_date
         */
        $latestTransactionForAccounts = [];
        foreach ($bts as $bt) {
            $latestTransactionForAccounts[$bt[0]->getAccount()->getId()] = $bt['latestTransactionDate'];
        }

        // need to grab a list of all members with current notifications
        $outstandingNotifications = $membershipStatusNotificationRepository->findOutstandingNotifications();
        /*
            Results data format
            [user_id, ...]
        */
        $memberIdsForCurrentNotifications = [];
        foreach ($outstandingNotifications as $membershipStatusNotification) {
            $memberIdsForCurrentNotifications[] = $membershipStatusNotification->getUser()->getId();
        }

        // grab the users in each of the various role states we need to audit
        $awatingMembers = $roleRepository->findOneByName(Role::MEMBER_PAYMENT)->getUsers();
        $currentMembers = $roleRepository->findOneByName(Role::MEMBER_CURRENT)->getUsers();
        $youngMembers = $roleRepository->findOneByName(Role::MEMBER_YOUNG)->getUsers();
        $exMembers = $roleRepository->findOneByName(Role::MEMBER_EX)->getUsers();

        // now we have the data we need from the DB setup some working vars
        $approveUsers = [];
        $warnUsers = [];
        $revokeUsers = [];
        $reinstateUsers = [];
        $ohCrapUsers = [];
        $notificationRevokeUsers = [];
        $notificationPaymentUsers = [];

        // this will be the server time the we run, might need to shift time portion to end of the day 23:59
        $dateNow = Carbon::now();
        $dateNow->setTime(0, 0, 0);
        $warnDate = clone $dateNow;
        $warnDate->sub(CarbonInterval::instance(new \DateInterval($metaRepository->get('audit_warn_interval', 'P1M14D'))));
        $revokeDate = clone $dateNow;
        $revokeDate->sub(
            CarbonInterval::instance(new \DateInterval($metaRepository->get('audit_revoke_interval', 'P2M')))
        );

        foreach ($awatingMembers as $user) {
            if (isset($latestTransactionForAccounts[$user->getAccount()->getId()])) {
                $transactionDate = $latestTransactionForAccounts[$user->getAccount()->getId()];
            } else {
                $transactionDate = null;
            }

            if ($transactionDate === null) {
                continue; // not paid us yet nothing to do here
            } elseif ($transactionDate > $revokeDate) { // transaction date is newer than revoke date
                // approve member
                $approveUsers[] = $user;
            } else { // transaction date is older than revoke date
                // why have they not yet been approved yet tell the admins
                $ohCrapUsers[] = $user;
            }
        }

        foreach ($currentMembers as $user) {
            if (isset($latestTransactionForAccounts[$user->getAccount()->getId()])) {
                $transactionDate = $latestTransactionForAccounts[$user->getAccount()->getId()];
            } else {
                $transactionDate = null;
            }

            if ($transactionDate === null) {
                // current member that has never paid us?
                // tell the admins
                $ohCrapUsers[] = $user;
            } elseif ($transactionDate < $revokeDate) { // transaction date is older than revoke date
                // make ex member
                $revokeUsers[] = $user;
                // clear notification if needed
                $notificationRevokeUsers[] = $user;
            } elseif ($transactionDate < $warnDate) { // transaction date is older than warning date
                // if not already warned
                if (! in_array($user->getId(), $memberIdsForCurrentNotifications)) {
                    // warn membership may be terminated if we don't see one soon
                    $warnUsers[] = $user;
                }
            } else {
                // date diff should be less than 1.5 months
                // clear any out standing warnings
                if (in_array($user->getId(), $memberIdsForCurrentNotifications)) {
                    $notificationPaymentUsers[] = $user;
                }
            }
        }

        foreach ($youngMembers as $user) {
            if (isset($latestTransactionForAccounts[$user->getAccount()->getId()])) {
                $transactionDate = $latestTransactionForAccounts[$user->getAccount()->getId()];
            } else {
                $transactionDate = null;
            }

            if ($transactionDate === null) {
                // current member that has never paid us?
                // tell the admins
                $ohCrapUsers[] = $user;
            } elseif ($transactionDate < $revokeDate) { // transaction date is older than revoke date
                // make ex member
                $revokeUsers[] = $user;
                // clear notification if needed
                $notificationRevokeUsers[] = $user;
            } elseif ($transactionDate < $warnDate) { // transaction date is older than warning date
                // if not already warned
                if (! in_array($user->getId(), $memberIdsForCurrentNotifications)) {
                    // warn membership may be terminated if we don't see one soon
                    $warnUsers[] = $user;
                }
            } else {
                // date diff should be less than 1.5 months
                // clear any out standing warnings
                if (in_array($user->getId(), $memberIdsForCurrentNotifications)) {
                    $notificationPaymentUsers[] = $user;
                }
            }
        }

        foreach ($exMembers as $user) {
            if (isset($latestTransactionForAccounts[$user->getAccount()->getId()])) {
                $transactionDate = $latestTransactionForAccounts[$user->getAccount()->getId()];
            } else {
                $transactionDate = null;
            }

            if ($transactionDate > $revokeDate) { // transaction date is newer than revoke date
                // reinstate member
                $reinstateUsers[] = $user;
            }
        }

        // right should now have 5 arrays of Id's to go and process
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

        foreach ($warnUsers as $user) {
            event(new MembershipPaymentWarning($user));
        }

        foreach ($revokeUsers as $user) {
            event(new NonPaymentOfMembership($user));
        }

        foreach ($reinstateUsers as $user) {
            event(new ReinstatementOfMembershipPayment($user));
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

        // need to delay the results processing to make sure NewMembershipPaidFor events have been processed and new users have pins, using a job to help with the delay
        AuditResultJob::dispatch(
            $approveUsers,
            $warnUsers,
            $revokeUsers,
            $reinstateUsers,
            count($notificationPaymentUsers)
        )->delay(now()->addMinutes(1));
    }
}
