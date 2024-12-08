<?php

namespace App\Providers;

use HMS\Helpers\Discord;
use HMS\Helpers\Features;
use HMS\Helpers\SiteVisitor;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\ServiceProvider;

class HmsHelperServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot(): void
    {
        //
    }

    /**
     * Register the application services.
     */
    public function register(): void
    {
        $this->app->bind('Features', function () {
            return new Features;
        });

        $this->app->bind('SiteVisitor', function () {
            return new SiteVisitor;
        });

        $this->app->bind('Discord', function (Application $app) {
            return new Discord(
                $app->config->get('services.discord.token'),
                $app->config->get('services.discord.guild_id')
            );
        });
    }
}
