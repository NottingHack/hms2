<?php

namespace App\Providers;

use Doctrine\DBAL\Types\Type;
use HMS\Doctrine\CarbonType;
use Illuminate\Support\ServiceProvider;

class DoctrineTypeServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        Type::overrideType('datetime', CarbonType::class);
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {

    }
}
