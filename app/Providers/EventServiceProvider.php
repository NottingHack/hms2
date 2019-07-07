<?php

namespace App\Providers;

use App\Listeners\RoleUpdateLogger;
use Illuminate\Support\Facades\Event;
use App\Listeners\ViMbAdminSubscriber;
use Illuminate\Auth\Events\Registered;
use App\Listeners\PrintLabelSubscriber;
use App\Listeners\Tools\NotifyNhToolsSubscriber;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
    ];

    /**
     * The subscriber classes to register.
     *
     * @var array
     */
    protected $subscribe = [
        ViMbAdminSubscriber::class,
        RoleUpdateLogger::class,
        PrintLabelSubscriber::class,
        NotifyNhToolsSubscriber::class,
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

    /**
     * Determine if events and listeners should be automatically discovered.
     *
     * @return bool
     */
    public function shouldDiscoverEvents()
    {
        return true;
    }
}
