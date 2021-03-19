<?php

namespace App\Providers;

use HMS\Auth\PasswordStore;
use HMS\Auth\PasswordStoreManager;
use HMS\Facades\Features;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use Stripe\Stripe;

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

        Blade::directive('fatureState', function ($value) {
            return "<?php echo Features::isEnabled({$value}) ? 'true' : 'false' ?>";
        });

        // @content('view', 'block')
        Blade::directive('content', function ($expression) {
            [$view, $block] = explode(', ', str_replace('\'', '', $expression));

            return "<?php echo Content::get('{$view}', '{$block}', 'ContentBlock missing for {$view}:{$block}  '); ?>";
        });
    }
}
