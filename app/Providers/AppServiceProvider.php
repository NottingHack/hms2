<?php

namespace App\Providers;

use Stripe\Stripe;
use HMS\Facades\Features;
use HMS\Auth\PasswordStore;
use HMS\Auth\PasswordStoreManager;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(PasswordStore::class, function ($app) {
            $passwordStoreManager = new PasswordStoreManager($app);

            return $passwordStoreManager->driver();
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // Globally set the money format
        setlocale(LC_MONETARY, 'en_GB.UTF-8');

        // setup Guzzle6 for mailgun batch sender
        $this->app->bind('mailgun.client', function () {
            return \Http\Adapter\Guzzle6\Client::createWithConfig([
                // your Guzzle6 configuration
            ]);
        });

        if (config('services.stripe.secret')) {
            Stripe::setApiKey(config('services.stripe.secret'));
        }

        Blade::if('feature', function ($value) {
            return Features::isEnabled($value);
        });

        Blade::directive('featureState', function ($value) {
            return '<?php echo Features::isEnabled(' . $value . ') ? \'true\' : \'false\' ?>';
        });
    }
}
