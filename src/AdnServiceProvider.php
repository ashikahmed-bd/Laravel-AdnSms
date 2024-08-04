<?php

namespace Ashik\AdnSms;

use Illuminate\Support\ServiceProvider;

class AdnServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/adn-sms.php', 'adn-sms'
        );

       app()->bind('adn-sms', function ($app){
           return new AdnSms();
       });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->publishes([
            __DIR__.'/../config/adn-sms.php' => config_path('adn-sms.php'),
        ], 'adn-sms-config');
    }

}
