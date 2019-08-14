<?php

namespace App\Providers;

use Stripe\Stripe;
use HMS\Auth\PasswordStore;
use Faker\Factory as FakerFactory;
use HMS\Auth\PasswordStoreManager;
use Faker\Generator as FakerGenerator;
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

        $this->app->singleton(FakerGenerator::class, function ($app) {
            return FakerFactory::create($app['config']->get('app.faker_locale', 'en_US'));
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

        // helper for formatting pennies
        \Blade::directive('format_pennies', function ($pennies) {
            return "<?php echo money_format('%n', (" . $pennies . ')/100); ?>';
        });

        // setup Guzzle6 for mailgun batch sender
        $this->app->bind('mailgun.client', function () {
            return \Http\Adapter\Guzzle6\Client::createWithConfig([
                // your Guzzle6 configuration
            ]);
        });

        if (config('services.stripe.secret')) {
            Stripe::setApiKey(config('services.stripe.secret'));
        }
    }
}
