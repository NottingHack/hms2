<?php

namespace App\Listeners\Banking;

use Carbon\Carbon;
use HMS\Entities\Role;
use Carbon\CarbonInterval;
use App\Events\Banking\AuditRequest;
use HMS\Repositories\MetaRepository;
use HMS\Repositories\RoleRepository;
use Illuminate\Queue\InteractsWithQueue;
use App\Notifications\Banking\AuditIssues;
use App\Notifications\Banking\AuditResult;
use HMS\Repositories\RoleUpdateRepository;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Events\Banking\NewMembershipPaidFor;
use App\Events\Banking\NonPaymentOfMembership;
use HMS\Repositories\GateKeeper\PinRepository;
use App\Events\Banking\MembershipPaymentWarning;
use HMS\Repositories\GateKeeper\AccessLogRepository;
use HMS\Repositories\Banking\BankTransactionRepository;
use App\Events\Banking\ReinstatementOfMembershipPayment;
use HMS\Repositories\Banking\MembershipStatusNotificationRepository;

class MembershipAudit implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * @var BankTransactionRepository
     */
    protected $bankTransactionRepository;

    /**
     * @var MembershipStatusNotificationRepository
     */
    protected $membershipStatusNotificationRepository;

    /**
     * @var MetaRepository
     */
    protected $metaRepository;

    /**
     * @var RoleRepository
     */
    protected $roleRepository;

    /**
     * @var AccessLogRepository
     */
    protected $accessLogRepository;

    /**
     * @var RoleUpdateRepository
     */
    protected $roleUpdateRepository;

    /**
     * @var PinRepository
     */
    protected $pinRepository;

    /**
     * Create a new event listener.
     *
     * @param BankTransactionRepository              $bankTransactionRepository
     * @param MembershipStatusNotificationRepository $membershipStatusNotificationRepository
     * @param MetaRepository                         $metaRepository
     * @param RoleRepository                         $roleRepository
     * @param AccessLogRepository                    $accessLogRepository
     * @param RoleUpdateRepository                   $roleUpdateRepository
     * @param PinRepository                          $pinRepository
     */
    public function __construct(BankTransactionRepository $bankTransactionRepository,
        MembershipStatusNotificationRepository $membershipStatusNotificationRepository,
        MetaRepository $metaRepository,
        RoleRepository $roleRepository,
        AccessLogRepository $accessLogRepository,
        RoleUpdateRepository $roleUpdateRepository,
        PinRepository $pinRepository)
    {
        $this->bankTransactionRepository = $bankTransactionRepository;
        $this->membershipStatusNotificationRepository = $membershipStatusNotificationRepository;
        $this->metaRepository = $metaRepository;
        $this->roleRepository = $roleRepository;
        $this->accessLogRepository = $accessLogRepository;
        $this->roleUpdateRepository = $roleUpdateRepository;
        $this->pinRepository = $pinRepository;
    }

    /**
     * Handle the event.
     *
     * @param  AuditRequest $event
     * @return mixed
     */
    public function handle(AuditRequest $event)
    {
        // get the latest transaction date for all accounts, store in $latestTransactionForAccounts
        $bts = $this->bankTransactionRepository->findLatestTransactionForAllAccounts();
        /*
            Results data format
            [account_id] => transaction_date
         */
        $latestTransactionForAccounts = [];
        foreach ($bts as $bt) {
            $latestTransactionForAccounts[$bt[0]->getAccount()->getId()] = $bt['latestTransactionDate'];
        }

        // need to grab a list of all members with current notifications
        $outstandingNotifications = $this->membershipStatusNotificationRepository->findOutstandingNotifications();
        /*
            Results data format
            [user_id, ...]
        */
        $memberIdsForCurrentNotifications = [];
        foreach ($outstandingNotifications as $membershipStatusNotification) {
            $memberIdsForCurrentNotifications[] = $membershipStatusNotification->getUser()->getId();
        }

        // grab the users in each of the various role states we need to audit
        $awatingMembers = $this->roleRepository->findOneByName(Role::MEMBER_PAYMENT)->getUsers();
        $currentMembers = $this->roleRepository->findOneByName(Role::MEMBER_CURRENT)->getUsers();
        $youngMembers = $this->roleRepository->findOneByName(Role::MEMBER_YOUNG)->getUsers();
        $exMembers = $this->roleRepository->findOneByName(Role::MEMBER_EX)->getUsers();

        // now we have the data we need from the DB setup some working vars
        $approveUsers = [];
        $warnUsers = [];
        $revokeUsers = [];
        $reinstateUsers = [];
        $ohCrapUsers = [];
        $notificationRevokeUsers = [];
        $notificationPaymentUsers = [];

        $dateNow = Carbon::now(); // this will be the server time the we run, might need to shift time portion to end of the day 23:59
        $dateNow->setTime(0, 0, 0);
        $warnDate = clone $dateNow;
        $warnDate->sub(CarbonInterval::instance(new \DateInterval($this->metaRepository->get('audit_warn_interval'))));
        $revokeDate = clone $dateNow;
        $revokeDate->sub(CarbonInterval::instance(new \DateInterval($this->metaRepository->get('audit_revoke_interval'))));

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
                if ( ! in_array($user->getId(), $memberIdsForCurrentNotifications)) {
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
                if ( ! in_array($user->getId(), $memberIdsForCurrentNotifications)) {
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
            $softwareTeamRole = $this->roleRepository->findOneByName(Role::TEAM_SOFTWARE);
            $softwareTeamRole->notify(new AuditIssues($ohCrapUsers));
        }

        // before sending out team emails clean up the warnings for people that have now paid us
        foreach ($notificationPaymentUsers as $user) {
            $userNotifications = $this->membershipStatusNotificationRepository->findOutstandingNotificationsByUser($user);
            foreach ($userNotifications as $notification) {
                $notification->clearNotificationsByPayment();
                $this->membershipStatusNotificationRepository->save($notification);
            }
        }

        // now email the audit results
        $auditResultNotification = new AuditResult($approveUsers,
            $warnUsers,
            $revokeUsers,
            $reinstateUsers,
            count($notificationPaymentUsers),
            $this->accessLogRepository,
            $this->bankTransactionRepository,
            $this->pinRepository,
            $this->roleUpdateRepository,
            $this->roleRepository
            );

        $membershipTeamRole = $this->roleRepository->findOneByName(Role::TEAM_MEMBERSHIP);
        $membershipTeamRole->notify($auditResultNotification);

        $trusteesTeamRole = $this->roleRepository->findOneByName(Role::TEAM_TRUSTEES);
        $trusteesTeamRole->notify($auditResultNotification);
    }
}
