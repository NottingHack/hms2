<?php

namespace App\Providers;

use Illuminate\Support\Facades\Event;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        'Illuminate\Auth\Events\Registered' => [
            'App\Listeners\Invites\RevokeInviteOnUserRegistered',
            'App\Listeners\Membership\ApprovalEmailOnUserRegistered',
        ],
        'App\Events\MembershipInterestRegistered' => [
            'App\Listeners\Invites\MailInvite',
        ],

        'Illuminate\Mail\Events\MessageSending' => [
            'App\Listeners\LogSentMessage',
        ],

        'App\Events\Banking\AuditRequest' => [
            'App\Listeners\Banking\MembershipAudit',
        ],

        'App\Events\Banking\NewMembershipPaidFor' => [
            'App\Listeners\Membership\ApproveNewMembership',
        ],

        'App\Events\Banking\MembershipPaymentWarning' => [
            'App\Listeners\Membership\WarnMembershipMayExpire',
        ],

        'App\Events\Banking\NonPaymentOfMembership' => [
            'App\Listeners\Membership\RevokeMembership',
        ],

        'App\Events\Banking\ReinstatementOfMembershipPayment' => [
            'App\Listeners\Membership\ReinstateMembership',
        ],

        'App\Events\Banking\TransactionsUploaded' => [
            'App\Listeners\Banking\SaveNewTransactions'
        ],
    ];

    /**
     * The subscriber classes to register.
     *
     * @var array
     */
    protected $subscribe = [
        'App\Listeners\ViMbAdminSubscriber',
        'App\Listeners\RoleUpdateLogger',
        'App\Listeners\PrintLabelSubscriber',
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        //
    }
}
