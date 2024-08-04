# ADN SMS Laravel

ADN SMS gateway API package for Laravel.

## Installation

You can install the package via composer in your Laravel or Lumen application.

```bash
composer require ashik/adn-sms
```
### Laravel

Publish the configuration file `config/adn-sms.php` where you can tweak some default options.

```bash
php artisan vendor:publish --tag=adn-sms-config
```

Define your `ADN_SMS_KEY` and `ADN_SMS_SECRET` in the `.env` file or update the `config/adn-sms.php` file of your application.

## Configuration

First have a look at the `./config/adn-sms.php` to know about all the options available out of the box. Some important ones are mentioned below.

**Service Enable/Disable**

You can easily turn off the whole service and any API calls from the config file. Comes in handy if the APIs are down or ongoing maintenance. Remember, if you set `enabled` to `false` in the configuration file, response body will always be empty string `$response->body() = ""`.

## Usage

### Single SMS

**Send single SMS to single recipient.**

``` php
use Ashik\AdnSms\Facades\AdnSms;

class SmsController
{
    /**
     * Make request and return response
     *
     * @return \Illuminate\Http\Client\Response
     * @throws \Throwable
     */
    public function send()
    {
        $response = AdnSms::to('01XXXXXXXXX')
            ->message('Send SMS Test.')
            ->send();
        
        return $response->json();
    }
}
```

### OTP SMS

**Send OTP SMS to single recipient.**

``` php
use Ashik\AdnSms\Facades\AdnSms;

class SmsController
{
    /**
     * Make request and return response
     *
     * @return \Illuminate\Http\Client\Response
     * @throws \Throwable
     */
    public function otp()
    {
        $response = AdnSms::otp('01XXXXXXXXX')
            ->message('Send OTP SMS Test.')
            ->send();

        return $response->json();
    }
}
```

### Bulk SMS

**Send single SMS to multiple recipients.**

Bulk SMS sending requires `campaignTitle()` as mandatory.

``` php
use Ashik\AdnSms\Facades\AdnSms;

class SmsController
{
    /**
     * Make request and return response
     *
     * @return \Illuminate\Http\Client\Response
     * @throws \Throwable
     */
    public function bulk()
    {
        $response = AdnSms::bulk(['019XXXXXXXX', '015XXXXXXXX'])
            ->campaignTitle('Bulk SMS Test')
            ->message('Send Bulk SMS Test.')
            ->send();
        
        return $response->json();
    }
}
```

### Queue SMS Sending (Laravel)

You can also queue SMS sending. You can pass a callback function in the `queue()` method to receive the `$response` from the API call and process it further. It is really useful if you want to save the response in the database after queue executes. See example for finer detail.

``` php
use Ashik\AdnSms\Facades\AdnSms;
use Illuminate\Http\Client\Response;
use App\Models\Table;

class SmsController
{
    public function bulk()
    {
        AdnSms::bulk(['019XXXXXXXXX', '02XXXXXXXXX'])
            ->campaignTitle('Bulk SMS Test')
            ->message('Send Bulk SMS Test.')
            ->queue(function (Response $response) {
                // Check if the response body is empty
                if ($response->body() != "") {
                    // Store the $response in the database
                    $model = new Table();
                    $model->data = $response->body();
                    $model->save();
                }
            });
    }
}
```

Do not forget to run `php artisan queue:work`.

**IMPORTANT:** `queue()` method is not available in Lumen as it does not supports queueable closer. However, you can create a queueable job in your Lumen application to do something similar. Call the `send()` method from your job and process the returned response further.

### Check Balance

``` php
use Ashik\AdnSms\Facades\AdnSms;

class SmsController
{
    /**
     * Make request and return response
     *
     * @return \Illuminate\Http\Client\Response
     * @throws \Throwable
     */
    public function someFunction()
    {
        $response = AdnSms::checkBalance();
        
        return $response->json();
    }
}
```

### Check SMS Status

To check already sent SMS status, simply call the `checkSmsStatus()` method with SMS UID.

``` php
use Ashik\AdnSms\Facades\AdnSms;

class SmsController
{
    /**
     * Make request and return response
     *
     * @return \Illuminate\Http\Client\Response
     * @throws \Throwable
     */
    public function smsStatus()
    {
        $response = AdnSms::checkSmsStatus('SXXXXXXXXXXXXXXXX');
        
        return $response->json();
    }
}
```

### Check Campaign Status

To check already sent SMS campaign status, simply call the `checkCampaignStatus()` method with campaign UID.

``` php
use Ashik\AdnSms\Facades\AdnSms;

class SmsController
{
    /**
     * Make request and return response
     *
     * @return \Illuminate\Http\Client\Response
     * @throws \Throwable
     */
    public function campaignStatus()
    {
        $response = AdnSms::checkCampaignStatus('CXXXXXXXXXXXXXXXX');
        
        return $response->json();
    }
}
```

### Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

### Security

If you discover any security related issues, please email at rahulhaque07@gmail.com instead of using the issue tracker.

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
