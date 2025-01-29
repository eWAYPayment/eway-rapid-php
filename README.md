# Eway Rapid PHP Library

[![Github Workflows][ico-workflow]][link-workflow]
[![Latest Version on Packagist][ico-version]][link-packagist]
[![Software License][ico-license]](LICENSE.md)

A PHP library to integrate with Eway's Rapid Payment API.

Sign up with Eway at:
 - Australia:    https://www.eway.com.au/
 - New Zealand:  https://eway.io/nz/
 - UK:           https://eway.io/uk/
 - Hong Kong:    https://eway.io/hk/
 - Malaysia:     https://eway.io/my/

For testing, get a free Eway Partner account: https://www.eway.com.au/partner-program/

## Install

This library requires PHP version 7.4.0 or greater, with the curl, json and openssl extensions.

### Using Composer

The Eway PHP SDK can be install via [Composer](https://getcomposer.org/) - this is the recommended method

```bash
$ composer require eway/eway-rapid-php
```

Then use Composer's autoload to include the library:

```php
require_once 'vendor/autoload.php';
```

### Manual

The eWAY PHP SDK can also be downloaded and added without Composer:

1. Download the [latest zip](https://github.com/eWAYPayment/eway-rapid-php/archive/master.zip) (or `git clone` this repository)
2. Unzip the zip into your project - for example into a `lib` directory
3. Include the Eway SDK:

```php
require_once 'lib/eway-rapid-php-master/include_eway.php';
```

## Usage

See the [Eway Rapid API Reference](https://eway.io/api-v3/?php) for usage details.

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
    ],
    'TransactionType' => \Eway\Rapid\Enum\TransactionType::PURCHASE,
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
$ composer run test-unit # Run unit tests
$ composer run test-integration # Run integration tests
```

This library follows PSR2 coding standards, run this command to check:

```bash
$ composer run phpcs
```

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

[ico-workflow]: https://github.com/eWAYPayment/eway-rapid-php/actions/workflows/phpunit.yml/badge.svg
[ico-version]: https://img.shields.io/packagist/v/eway/eway-rapid-php.svg?style=flat-square
[ico-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square

[link-workflow]: https://github.com/eWAYPayment/eway-rapid-php/actions
[link-packagist]: https://packagist.org/packages/eway/eway-rapid-php
