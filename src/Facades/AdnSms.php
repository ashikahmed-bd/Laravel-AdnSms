<?php

namespace Ashik\AdnSms\Facades;

use Illuminate\Support\Facades\Facade;

class AdnSms extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'adn-sms';

    }
}
