<?php

namespace App\Providers;

use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Tremby\LaravelGitVersion\GitVersionHelper;

class ComposerServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        View::composer(
            'layouts.navbar',
            'HMS\Composers\NavComposer'
        );

        View::composer(
            'card.proxy',
            'HMS\Composers\ProxyComposer'
        );

        View::composer('layouts.footer', function ($view) {
            $gitDescribe = GitVersionHelper::getVersion();
            $parts = preg_match('/^(?:(.*)-(\d+)-g)?([a-fA-F0-9]{7,40})(-dirty)?$/', $gitDescribe, $matches);

            if ($parts == 0) {
                // we are on the tag commit
                $version = $gitDescribe;
            } else {
                if ($matches[1] == '') {
                    // there are no tags so use commit hash
                    $version = $matches[3];
                } else {
                    // tag and commits since it
                    $version = $matches[1] . '-' . $matches[2];
                }
                if (array_key_exists(4, $matches)) {
                    // are we dirty?
                    $version .= $matches[4];
                }
            }

            $view->with('version', $version);
        });
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
