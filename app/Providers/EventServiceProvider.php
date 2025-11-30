<?php

namespace App\Providers;

use App\Listeners\Governance\RegisterOfDirectorsLogger;
use App\Listeners\Governance\RegisterOfMembersLogger;
use App\Listeners\PrintLabelSubscriber;
use App\Listeners\RoleUpdateDiscordUpdater;
use App\Listeners\RoleUpdateLogger;
use App\Listeners\Tools\NotifyNhToolsSubscriber;
use App\Listeners\ViMbAdminSubscriber;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
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
        RoleUpdateDiscordUpdater::class,
        RegisterOfDirectorsLogger::class,
        RegisterOfMembersLogger::class,
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        //
    }
}
