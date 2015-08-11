# eWAY Rapid PHP Library

A PHP library to integrate with eWAY's Rapid Payment API.

Sign up with eWAY at:
 - Australia:    https://www.eway.com.au/
 - New Zealand:  https://eway.io/nz/
 - UK:           https://eway.io/uk/
 - Hong Kong:    https://eway.io/hk/
 - Malayasia:    https://eway.io/my/
 - Singapore:    https://eway.io/sg/

For testing, get a free eWAY Partner account: https://www.eway.com.au/developers

## Install

This library requires PHP version 5.4.0 or greater, with the curl, json and openssl extensions.

Via [Composer](https://getcomposer.org/)

```bash
$ composer require eway/eway-rapid-php
```

Then use Composer's autoload to include the library:

```php
require('vendor/autoload.php');
```

## Usage

See the [eWAY Rapid API Reference](https://eway.io/api-v3/) for usage details.

A simple Direct payment:

```php
require('vendor/autoload.php');

$apiKey = 'YOUR-API-KEY';
$apiPassword = 'YOUR-API-PASSWORD';
$apiEndpoint = \Eway\Rapid\Client::MODE_SANDBOX;
$client = \Eway\Rapid::createClient($apiKey, $apiPassword, $apiEndpoint);

$transaction = [
    'Customer' => [
        'CardDetails' => [
            'Name' => 'John Smith',
            'Number' => '4444333322221111',
            'ExpiryMonth' => '12',
            'ExpiryYear' => '25',
            'CVN' => '123',
        ]
    ],
    'Payment' => [
        'TotalAmount' => 1000,
    ]
];

$response = $client->createTransaction(\Eway\Rapid\Enum\ApiMethod::DIRECT, $transaction);
if ($response->TransactionStatus) {
    echo 'Payment successful! ID: '.$response->TransactionID;
}
```

## Change log

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Testing

Tests are written with [PHPUnit](https://phpunit.de/). They can be run using Composer:

```bash
$ composer test
```

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.