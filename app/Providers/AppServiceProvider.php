<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use SocialiteProviders\Manager\SocialiteWasCalled;
use Illuminate\Support\Facades\Event;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        // Register Apple Socialite provider
        Event::listen(function (SocialiteWasCalled $event) {
            $event->extendSocialite('apple', \SocialiteProviders\Apple\Provider::class);
        });
    }
}
