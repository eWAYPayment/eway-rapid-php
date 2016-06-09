<?php

namespace Eway\Test\Unit\Client;

use Eway\Rapid\Contract\Client;
use Eway\Rapid\Contract\HttpService;
use Eway\Rapid\Enum\ApiMethod;
use Eway\Rapid\Enum\PaymentMethod;
use Eway\Rapid\Enum\TransactionType;
use Eway\Rapid\Model\Response\CreateTransactionResponse;
use Eway\Test\AbstractClientTest;
use Prophecy\Argument;
use Prophecy\Prophecy\MethodProphecy;

/**
 * Class CreateTransactionTest.
 */
class CreateTransactionTest extends AbstractClientTest
{
    public function testCreateTransactionDirectConnectionCaptureOn()
    {
        $customer = [
            'CardDetails' => [
                'Name' => 'John Smith',
                'Number' => '4444333322221111',
                'ExpiryMonth' => '12',
                'ExpiryYear' => '25',
                'CVN' => '123',
            ],
        ];
        $payment = [
            'TotalAmount' => 1000,
        ];
        $transaction = [
            'Customer' => $customer,
            'Payment' => $payment,
            'Capture' => true,
            'TransactionType' => TransactionType::PURCHASE,
        ];

        $mockTransaction = $transaction;
        $mockTransaction['Method'] = PaymentMethod::PROCESS_PAYMENT;

        $mockResponse = $this->getResponse([
            'AuthorisationCode' => '123456',
            'ResponseCode' => '00',
            'ResponseMessage' => 'A2000',
            'TransactionID' => 12345678,
            'TransactionStatus' => true,
            'TransactionType' => 'Purchase',
            'BeagleScore' => 0,
            'Verification' => [
                'CVN' => 0,
                'Address' => 0,
                'Email' => 0,
                'Mobile' => 0,
                'Phone' => 0,
            ],
            'Customer' => [
                'CardDetails' => [
                    'Number' => '444433XXXXXX1111',
                    'Name' => 'John Smith',
                    'ExpiryMonth' => '12',
                    'ExpiryYear' => '25',
                    'StartMonth' => null,
                    'StartYear' => null,
                    'IssueNumber' => null,
                ],
                'TokenCustomerID' => null,
                'Reference' => '',
                'Title' => 'Mr.',
                'FirstName' => '',
                'LastName' => '',
                'CompanyName' => '',
                'JobDescription' => '',
                'Street1' => '',
                'Street2' => '',
                'City' => '',
                'State' => '',
                'PostalCode' => '',
                'Country' => '',
                'Email' => '',
                'Phone' => '',
                'Mobile' => '',
                'Comments' => '',
                'Fax' => '',
                'Url' => '',
            ],
            'Payment' => [
                'TotalAmount' => 1000,
                'InvoiceNumber' => '',
                'InvoiceDescription' => '',
                'InvoiceReference' => '',
                'CurrencyCode' => 'AUD',
            ],
            'Errors' => '',
        ]);

        $httpService = $this->prophet->prophesize('Eway\Rapid\Contract\HttpService');

        /** @var MethodProphecy $postTransactionStub */
        $postTransactionStub = $httpService->postTransaction(Argument::type('array'));
        $postTransactionStub->withArguments([$mockTransaction])->willReturn($mockResponse)->shouldBeCalled();

        /** @var HttpService $stub */
        $stub = $httpService->reveal();
        $this->client->setHttpService($stub);


        $response = $this->client->createTransaction(ApiMethod::DIRECT, $transaction);

        $this->assertInstanceOf(CreateTransactionResponse::getClass(), $response);
        $this->assertTrue(is_array($response->getErrors()));
        $this->assertEmpty($response->getErrors());
        $this->assertTrue($response->TransactionStatus);
        $this->assertCustomer($customer, $response);
    }

    public function testCreateTransactionDirectConnectionCaptureOff()
    {
        $customer = [
            'CardDetails' => [
                'Name' => 'John Smith',
                'Number' => '4444333322221111',
                'ExpiryMonth' => '12',
                'ExpiryYear' => '25',
                'CVN' => '123',
            ],
        ];
        $payment = [
            'TotalAmount' => 1000,
        ];
        $transaction = [
            'Customer' => $customer,
            'Payment' => $payment,
            'Capture' => false,
            'TransactionType' => TransactionType::PURCHASE,
        ];


        $mockTransaction = $transaction;
        $mockTransaction['Method'] = PaymentMethod::AUTHORISE;

        $mockResponse = $this->getResponse([
            'AuthorisationCode' => '123456',
            'ResponseCode' => '00',
            'ResponseMessage' => 'A2000',
            'TransactionID' => 12345678,
            'TransactionStatus' => true,
            'TransactionType' => 'Purchase',
            'BeagleScore' => 0,
            'Verification' => [
                'CVN' => 0,
                'Address' => 0,
                'Email' => 0,
                'Mobile' => 0,
                'Phone' => 0,
            ],
            'Customer' => [
                'CardDetails' => [
                    'Number' => '444433XXXXXX1111',
                    'Name' => 'John Smith',
                    'ExpiryMonth' => '12',
                    'ExpiryYear' => '25',
                    'StartMonth' => null,
                    'StartYear' => null,
                    'IssueNumber' => null,
                ],
                'TokenCustomerID' => null,
                'Reference' => '',
                'Title' => 'Mr.',
                'FirstName' => '',
                'LastName' => '',
                'CompanyName' => '',
                'JobDescription' => '',
                'Street1' => '',
                'Street2' => '',
                'City' => '',
                'State' => '',
                'PostalCode' => '',
                'Country' => '',
                'Email' => '',
                'Phone' => '',
                'Mobile' => '',
                'Comments' => '',
                'Fax' => '',
                'Url' => '',
            ],
            'Payment' => [
                'TotalAmount' => 1000,
                'InvoiceNumber' => '',
                'InvoiceDescription' => '',
                'InvoiceReference' => '',
                'CurrencyCode' => 'AUD',
            ],
            'Errors' => '',
        ]);

        $httpService = $this->prophet->prophesize('Eway\Rapid\Contract\HttpService');

        /** @var MethodProphecy $postTransactionStub */
        $postTransactionStub = $httpService->postTransaction(Argument::type('array'));
        $postTransactionStub->withArguments([$mockTransaction])->willReturn($mockResponse)->shouldBeCalled();

        /** @var HttpService $stub */
        $stub = $httpService->reveal();
        $this->client->setHttpService($stub);


        $response = $this->client->createTransaction(ApiMethod::DIRECT, $transaction);
        $this->assertInstanceOf(CreateTransactionResponse::getClass(), $response);
        $this->assertTrue(is_array($response->getErrors()));
        $this->assertEmpty($response->getErrors());
        $this->assertTrue($response->TransactionStatus);
        $this->assertCustomer($customer, $response);
    }

    public function testCreateTransactionResponsiveSharedCaptureOnCustomerHasToken()
    {
        $tokenCustomer = [
            'Title' => 'Mr.',
            'FirstName' => 'John',
            'LastName' => 'Smith',
            'Country' => 'au',
            'CardDetails' => [
                'Name' => 'John Smith',
                'Number' => '4444333322221111',
                'ExpiryMonth' => '12',
                'ExpiryYear' => '25',
                'CVN' => '123',
            ],
        ];

        $tokenCustomerId = 987654321098;
        $customer = [
            'TokenCustomerID' => $tokenCustomerId,
        ];

        $payment = [
            'TotalAmount' => 1000,
        ];
        $transaction = [
            'Customer' => $customer,
            'Payment' => $payment,
            'RedirectUrl' => 'http://www.eway.com.au',
            'CancelUrl' => 'http://www.eway.com.au',
            'TransactionType' => TransactionType::PURCHASE,
            'Capture' => true,
        ];

        $mockTransaction = $transaction;
        $mockTransaction['Method'] = PaymentMethod::TOKEN_PAYMENT;

        $mockAccessCode = '60CF3bz7XKEjY3fKwezFSojBRtWsxLitI4gbReJrhYaUkapWZ5p9_doUnlD4RlTsCig_xvSSn9yQh8H_qJVWqkRUyGWh4UGxqmCx-67LPC9K2B21QM8AhyLadPFB06Toswzgz';
        $mockResponse = $this->getResponse([
            'SharedPaymentUrl' => 'https://secure-au.sandbox.ewaypayments.com/sharedpage/sharedpayment?AccessCode='.$mockAccessCode,
            'AccessCode' => $mockAccessCode,
            'Customer' => [
                'CardNumber' => '444433XXXXXX1111',
                'CardStartMonth' => '',
                'CardStartYear' => '',
                'CardIssueNumber' => '',
                'CardName' => 'John Smith',
                'CardExpiryMonth' => '12',
                'CardExpiryYear' => '25',
                'IsActive' => true,
                'TokenCustomerID' => $tokenCustomerId,
                'Reference' => '',
                'Title' => 'Mr.',
                'FirstName' => 'John',
                'LastName' => 'Smith',
                'CompanyName' => '',
                'JobDescription' => '',
                'Street1' => '',
                'Street2' => '',
                'City' => '',
                'State' => '',
                'PostalCode' => '',
                'Country' => 'au',
                'Email' => '',
                'Phone' => '',
                'Mobile' => '',
                'Comments' => '',
                'Fax' => '',
                'Url' => '',
            ],
            'Payment' => [
                'TotalAmount' => $payment['TotalAmount'],
                'InvoiceNumber' => null,
                'InvoiceDescription' => null,
                'InvoiceReference' => null,
                'CurrencyCode' => 'AUD',
            ],
            'FormActionURL' => 'https://secure-au.sandbox.ewaypayments.com/AccessCode/'.$mockAccessCode,
            'CompleteCheckoutURL' => null,
            'Errors' => '',
        ]);

        $httpService = $this->prophet->prophesize('Eway\Rapid\Contract\HttpService');

        /** @var MethodProphecy $postAccessCodeSharedStub */
        $postAccessCodeSharedStub = $httpService->postAccessCodeShared(Argument::type('array'));
        $postAccessCodeSharedStub->withArguments([$mockTransaction])->willReturn($mockResponse)->shouldBeCalled();

        /** @var HttpService $stub */
        $stub = $httpService->reveal();
        $this->client->setHttpService($stub);


        $response = $this->client->createTransaction(ApiMethod::RESPONSIVE_SHARED, $transaction);

        $this->assertInstanceOf(CreateTransactionResponse::getClass(), $response);
        $this->assertTrue(is_array($response->getErrors()));
        $this->assertEmpty($response->getErrors());
        $this->assertCustomer($tokenCustomer, $response, true);
        $this->assertEquals($customer['TokenCustomerID'], $response->Customer->TokenCustomerID);
        $this->assertPayment($payment, $response);
    }

    public function testCreateTransactionResponsiveSharedCaptureOnCustomerNoToken()
    {
        $customer = [
            'Title' => 'Mr.',
            'FirstName' => 'John',
            'LastName' => 'Smith',
            'Country' => 'au',
        ];

        $payment = [
            'TotalAmount' => 1000,
        ];
        $transaction = [
            'Customer' => $customer,
            'Payment' => $payment,
            'RedirectUrl' => 'http://www.eway.com.au',
            'CancelUrl' => 'http://www.eway.com.au',
            'TransactionType' => TransactionType::PURCHASE,
            'Capture' => true,
        ];

        $mockTransaction = $transaction;
        $mockTransaction['Method'] = PaymentMethod::PROCESS_PAYMENT;

        $mockAccessCode = 'C3AB9fzHiJd3KF-fCec1eTb58-l3lMs-NamzSPz13oUB8pRgSXvnE-WSFpnmf0G82C7TO6VXwHqKpoYliPHUdlJiTcOpYdkbM_DuLp3XXoVOG0HgSe4LdZ1DaUB9d9AJlFXcr';
        $mockResponse = $this->getResponse([
            'SharedPaymentUrl' => 'https://secure-au.sandbox.ewaypayments.com/sharedpage/sharedpayment?AccessCode='.$mockAccessCode,
            'AccessCode' => $mockAccessCode,
            'Customer' => [
                'CardNumber' => '',
                'CardStartMonth' => '',
                'CardStartYear' => '',
                'CardIssueNumber' => '',
                'CardName' => '',
                'CardExpiryMonth' => '',
                'CardExpiryYear' => '',
                'IsActive' => false,
                'TokenCustomerID' => null,
                'Reference' => '',
                'Title' => 'Mr.',
                'FirstName' => 'John',
                'LastName' => 'Smith',
                'CompanyName' => '',
                'JobDescription' => '',
                'Street1' => '',
                'Street2' => '',
                'City' => '',
                'State' => '',
                'PostalCode' => '',
                'Country' => 'au',
                'Email' => '',
                'Phone' => '',
                'Mobile' => '',
                'Comments' => '',
                'Fax' => '',
                'Url' => '',
            ],
            'Payment' => [
                'TotalAmount' => 1000,
                'InvoiceNumber' => null,
                'InvoiceDescription' => null,
                'InvoiceReference' => null,
                'CurrencyCode' => 'AUD',
            ],
            'FormActionURL' => 'https://secure-au.sandbox.ewaypayments.com/AccessCode/'.$mockAccessCode,
            'CompleteCheckoutURL' => null,
            'Errors' => '',
        ]);

        $httpService = $this->prophet->prophesize('Eway\Rapid\Contract\HttpService');

        /** @var MethodProphecy $postAccessCodeSharedStub */
        $postAccessCodeSharedStub = $httpService->postAccessCodeShared(Argument::type('array'));
        $postAccessCodeSharedStub->withArguments([$mockTransaction])->willReturn($mockResponse)->shouldBeCalled();

        /** @var HttpService $stub */
        $stub = $httpService->reveal();
        $this->client->setHttpService($stub);


        $response = $this->client->createTransaction(ApiMethod::RESPONSIVE_SHARED, $transaction);

        $this->assertInstanceOf(CreateTransactionResponse::getClass(), $response);
        $this->assertTrue(is_array($response->getErrors()));
        $this->assertEmpty($response->getErrors());
        $this->assertNotEmpty($response->SharedPaymentUrl);
        $this->assertNotEmpty($response->AccessCode);
        $this->assertNotEmpty($response->FormActionURL);
        $this->assertCustomer($customer, $response);
        $this->assertPayment($payment, $response);
    }

    public function testCreateTransactionResponsiveSharedCaptureOff()
    {
        $payment = [
            "TotalAmount" => 100,
        ];

        $transaction = [
            "Payment" => $payment,
            "RedirectUrl" => "http://www.eway.com.au",
            "CancelUrl" => "http://www.eway.com.au",
            "TransactionType" => TransactionType::PURCHASE,
            'Capture' => false,
        ];

        $mockTransaction = $transaction;
        $mockTransaction['Method'] = PaymentMethod::AUTHORISE;

        $mockAccessCode = 'C3AB9Aq9z_lS3pWCbmFID38b7tuc-bZ6nDnTOKf-glgXsdnQC8Xti4z5ZdaLOSpieHY0xW6eSNkXXbre1okprUL6r2zlRpd39g7edwAKI5AOz58jMQ1Zs1zQYhnRGWLSwQ3bQ';
        $mockResponse = $this->getResponse([
            'SharedPaymentUrl' => 'https://secure-au.sandbox.ewaypayments.com/sharedpage/sharedpayment?AccessCode='.$mockAccessCode,
            'AccessCode' => 'C3AB9Aq9z_lS3pWCbmFID38b7tuc-bZ6nDnTOKf-glgXsdnQC8Xti4z5ZdaLOSpieHY0xW6eSNkXXbre1okprUL6r2zlRpd39g7edwAKI5AOz58jMQ1Zs1zQYhnRGWLSwQ3bQ',
            'Customer' => [
                'CardNumber' => '',
                'CardStartMonth' => '',
                'CardStartYear' => '',
                'CardIssueNumber' => '',
                'CardName' => '',
                'CardExpiryMonth' => '',
                'CardExpiryYear' => '',
                'IsActive' => false,
                'TokenCustomerID' => null,
                'Reference' => '',
                'Title' => 'Mr.',
                'FirstName' => '',
                'LastName' => '',
                'CompanyName' => '',
                'JobDescription' => '',
                'Street1' => '',
                'Street2' => '',
                'City' => '',
                'State' => '',
                'PostalCode' => '',
                'Country' => '',
                'Email' => '',
                'Phone' => '',
                'Mobile' => '',
                'Comments' => '',
                'Fax' => '',
                'Url' => '',
            ],
            'Payment' => [
                'TotalAmount' => 100,
                'InvoiceNumber' => null,
                'InvoiceDescription' => null,
                'InvoiceReference' => null,
                'CurrencyCode' => 'AUD',
            ],
            'FormActionURL' => 'https://secure-au.sandbox.ewaypayments.com/AccessCode/C3AB9Aq9z_lS3pWCbmFID38b7tuc-bZ6nDnTOKf-glgXsdnQC8Xti4z5ZdaLOSpieHY0xW6eSNkXXbre1okprUL6r2zlRpd39g7edwAKI5AOz58jMQ1Zs1zQYhnRGWLSwQ3bQ',
            'CompleteCheckoutURL' => null,
            'Errors' => '',
        ]);

        $httpService = $this->prophet->prophesize('Eway\Rapid\Contract\HttpService');

        /** @var MethodProphecy $postAccessCodeSharedStub */
        $postAccessCodeSharedStub = $httpService->postAccessCodeShared(Argument::type('array'));
        $postAccessCodeSharedStub->withArguments([$mockTransaction])->willReturn($mockResponse)->shouldBeCalled();

        /** @var HttpService $stub */
        $stub = $httpService->reveal();
        $this->client->setHttpService($stub);


        $response = $this->client->createTransaction(ApiMethod::RESPONSIVE_SHARED, $transaction);

        $this->assertInstanceOf(CreateTransactionResponse::getClass(), $response);
        $this->assertTrue(is_array($response->getErrors()));
        $this->assertEmpty($response->getErrors());
        $this->assertPayment($payment, $response);
        $this->assertNotEmpty($response->SharedPaymentUrl);
        $this->assertNotEmpty($response->AccessCode);
    }

    public function testCreateTransactionTransparentRedirectCaptureOnCustomerHasToken()
    {
        $tokenCustomer = [
            'Title' => 'Mr.',
            'FirstName' => 'John',
            'LastName' => 'Smith',
            'Country' => 'au',
            'CardDetails' => [
                'Name' => 'John Smith',
                'Number' => '4444333322221111',
                'ExpiryMonth' => '12',
                'ExpiryYear' => '25',
                'CVN' => '123',
            ],
        ];

        $tokenCustomerId = 987654321098;

        $customer = [
            'TokenCustomerID' => $tokenCustomerId,
        ];

        $payment = [
            "TotalAmount" => 100,
        ];

        $transaction = [
            'Customer' => $customer,
            "Payment" => $payment,
            "RedirectUrl" => "http://www.eway.com.au",
            "TransactionType" => TransactionType::PURCHASE,
            'Capture' => true,
        ];


        $mockTransaction = $transaction;
        $mockTransaction['Method'] = PaymentMethod::TOKEN_PAYMENT;

        $accessCode = 'F9802noBls1ulbf7qgbJpaYM3-y1ZudjLmboK9F-0Eanr0zCOvalcC9yRaidydER-AuEbskbkCS8VX1_maEaCS0fwHoiK2TIjJJeMBJGBC19hqnXavsXMzwPpimF9lT8fCUWC';
        $mockResponse = $this->getResponse([
            'AccessCode' => $accessCode,
            'Customer' => [
                'CardNumber' => '444433XXXXXX1111',
                'CardStartMonth' => '',
                'CardStartYear' => '',
                'CardIssueNumber' => '',
                'CardName' => 'John Smith',
                'CardExpiryMonth' => '12',
                'CardExpiryYear' => '25',
                'IsActive' => true,
                'TokenCustomerID' => $tokenCustomerId,
                'Reference' => '',
                'Title' => 'Mr.',
                'FirstName' => 'John',
                'LastName' => 'Smith',
                'CompanyName' => '',
                'JobDescription' => '',
                'Street1' => '',
                'Street2' => '',
                'City' => '',
                'State' => '',
                'PostalCode' => '',
                'Country' => 'au',
                'Email' => '',
                'Phone' => '',
                'Mobile' => '',
                'Comments' => '',
                'Fax' => '',
                'Url' => '',
            ],
            'Payment' => [
                'TotalAmount' => $payment['TotalAmount'],
                'InvoiceNumber' => null,
                'InvoiceDescription' => null,
                'InvoiceReference' => null,
                'CurrencyCode' => 'AUD',
            ],
            'FormActionURL' => 'https://secure-au.sandbox.ewaypayments.com/AccessCode/'.$accessCode,
            'CompleteCheckoutURL' => null,
            'Errors' => '',
        ]);

        $httpService = $this->prophet->prophesize('Eway\Rapid\Contract\HttpService');

        /** @var MethodProphecy $postAccessCodeStub */
        $postAccessCodeStub = $httpService->postAccessCode(Argument::type('array'));
        $postAccessCodeStub->withArguments([$mockTransaction])->willReturn($mockResponse)->shouldBeCalled();

        /** @var HttpService $stub */
        $stub = $httpService->reveal();
        $this->client->setHttpService($stub);


        $response = $this->client->createTransaction(ApiMethod::TRANSPARENT_REDIRECT, $transaction);

        $this->assertInstanceOf(CreateTransactionResponse::getClass(), $response);
        $this->assertTrue(is_array($response->getErrors()));
        $this->assertEmpty($response->getErrors());
        $this->assertNotEmpty($response->AccessCode);
        $this->assertNotEmpty($response->FormActionURL);
        $this->assertCustomer($tokenCustomer, $response, true);
        $this->assertEquals($customer['TokenCustomerID'], $response->Customer->TokenCustomerID);
        $this->assertPayment($payment, $response);
    }

    public function testCreateTransactionTransparentRedirectCaptureOnCustomerNoToken()
    {
        $payment = [
            "TotalAmount" => 100,
        ];

        $transaction = [
            "Payment" => $payment,
            "RedirectUrl" => "http://www.eway.com.au",
            "TransactionType" => TransactionType::PURCHASE,
            'Capture' => true,
        ];


        $mockTransaction = $transaction;
        $mockTransaction['Method'] = PaymentMethod::PROCESS_PAYMENT;

        $accessCode = 'A1001JYKRe5LHxeV_Z8__ZE8cEcFgLd2XSIfZyhHQwm6MJya_9rqbqfoWiOEaARlVPEjRBnuUNFv595ZF1UJpjM2nHTAfqo17mDO5L3eVubz3Rf5TMRVg03ulr7o2iLqCrdzd';
        $mockResponse = $this->getResponse([
            'AccessCode' => $accessCode,
            'Customer' => [
                'CardNumber' => '',
                'CardStartMonth' => '',
                'CardStartYear' => '',
                'CardIssueNumber' => '',
                'CardName' => '',
                'CardExpiryMonth' => '',
                'CardExpiryYear' => '',
                'IsActive' => false,
                'TokenCustomerID' => null,
                'Reference' => '',
                'Title' => 'Mr.',
                'FirstName' => '',
                'LastName' => '',
                'CompanyName' => '',
                'JobDescription' => '',
                'Street1' => '',
                'Street2' => '',
                'City' => '',
                'State' => '',
                'PostalCode' => '',
                'Country' => '',
                'Email' => '',
                'Phone' => '',
                'Mobile' => '',
                'Comments' => '',
                'Fax' => '',
                'Url' => '',
            ],
            'Payment' => [
                'TotalAmount' => $payment['TotalAmount'],
                'InvoiceNumber' => null,
                'InvoiceDescription' => null,
                'InvoiceReference' => null,
                'CurrencyCode' => 'AUD',
            ],
            'FormActionURL' => 'https://secure-au.sandbox.ewaypayments.com/AccessCode/'.$accessCode,
            'CompleteCheckoutURL' => null,
            'Errors' => '',
        ]);

        $httpService = $this->prophet->prophesize('Eway\Rapid\Contract\HttpService');

        /** @var MethodProphecy $postAccessCodeStub */
        $postAccessCodeStub = $httpService->postAccessCode(Argument::type('array'));
        $postAccessCodeStub->withArguments([$mockTransaction])->willReturn($mockResponse)->shouldBeCalled();

        /** @var HttpService $stub */
        $stub = $httpService->reveal();
        $this->client->setHttpService($stub);


        $response = $this->client->createTransaction(ApiMethod::TRANSPARENT_REDIRECT, $transaction);

        $this->assertInstanceOf(CreateTransactionResponse::getClass(), $response);
        $this->assertTrue(is_array($response->getErrors()));
        $this->assertEmpty($response->getErrors());
        $this->assertNotEmpty($response->AccessCode);
        $this->assertNotEmpty($response->FormActionURL);
        $this->assertPayment($payment, $response);
    }

    public function testCreateTransactionTransparentRedirectCaptureOff()
    {
        $payment = [
            "TotalAmount" => 100,
        ];

        $transaction = [
            "Payment" => $payment,
            "RedirectUrl" => "http://www.eway.com.au",
            "TransactionType" => TransactionType::PURCHASE,
            'Capture' => false,
        ];


        $mockTransaction = $transaction;
        $mockTransaction['Method'] = PaymentMethod::AUTHORISE;

        $accessCode = '60CF3LxC7xGFXFmsdgZ6QTWEbsM5KBnloy27SBzAmKvmbSE0HwWR05rPY0dmLXipIGAB7rUOBDjYYOvhzzQXfcCeNQs-MOo4GWB-yrh4JQc5HmTOIyJMUQeg4ZqtQdCGY9CP4';
        $mockResponse = $this->getResponse([
            'AccessCode' => $accessCode,
            'Customer' => [
                'CardNumber' => '',
                'CardStartMonth' => '',
                'CardStartYear' => '',
                'CardIssueNumber' => '',
                'CardName' => '',
                'CardExpiryMonth' => '',
                'CardExpiryYear' => '',
                'IsActive' => false,
                'TokenCustomerID' => null,
                'Reference' => '',
                'Title' => 'Mr.',
                'FirstName' => '',
                'LastName' => '',
                'CompanyName' => '',
                'JobDescription' => '',
                'Street1' => '',
                'Street2' => '',
                'City' => '',
                'State' => '',
                'PostalCode' => '',
                'Country' => '',
                'Email' => '',
                'Phone' => '',
                'Mobile' => '',
                'Comments' => '',
                'Fax' => '',
                'Url' => '',
            ],
            'Payment' => [
                'TotalAmount' => $payment['TotalAmount'],
                'InvoiceNumber' => null,
                'InvoiceDescription' => null,
                'InvoiceReference' => null,
                'CurrencyCode' => 'AUD',
            ],
            'FormActionURL' => 'https://secure-au.sandbox.ewaypayments.com/AccessCode/'.$accessCode,
            'CompleteCheckoutURL' => null,
            'Errors' => '',
        ]);

        $httpService = $this->prophet->prophesize('Eway\Rapid\Contract\HttpService');

        /** @var MethodProphecy $postAccessCodeStub */
        $postAccessCodeStub = $httpService->postAccessCode(Argument::type('array'));
        $postAccessCodeStub->withArguments([$mockTransaction])->willReturn($mockResponse)->shouldBeCalled();

        /** @var HttpService $stub */
        $stub = $httpService->reveal();
        $this->client->setHttpService($stub);


        $response = $this->client->createTransaction(ApiMethod::TRANSPARENT_REDIRECT, $transaction);

        $this->assertInstanceOf(CreateTransactionResponse::getClass(), $response);
        $this->assertTrue(is_array($response->getErrors()));
        $this->assertEmpty($response->getErrors());
        $this->assertNotEmpty($response->AccessCode);
        $this->assertNotEmpty($response->FormActionURL);
        $this->assertPayment($payment, $response);
    }

    public function testCreateTransactionAuthorisationShouldCallHttpServicePostCapturePayment()
    {
        $payment = [
            'TotalAmount' => 1000,
        ];

        $transaction = [
            'Payment' => $payment,
            'TransactionID' => 'foo',
        ];

        $mockTransaction = $transaction;
        $mockTransaction['Capture'] = true;

        $mockResponse = $this->getResponse([
            'ResponseCode' => '123456',
            'ResponseMessage' => '234567',
            'TransactionID' => 12345678,
            'TransactionStatus' => true,
            'Errors' => '',
        ]);

        $httpService = $this->prophet->prophesize('Eway\Rapid\Contract\HttpService');

        /** @var MethodProphecy $postCapturePaymentStub */
        $postCapturePaymentStub = $httpService->postCapturePayment(Argument::type('array'));
        $postCapturePaymentStub->withArguments([$mockTransaction])->willReturn($mockResponse)->shouldBeCalled();

        /** @var HttpService $stub */
        $stub = $httpService->reveal();
        $this->client->setHttpService($stub);


        $response = $this->client->createTransaction(ApiMethod::AUTHORISATION, $transaction);

        $this->assertInstanceOf(CreateTransactionResponse::getClass(), $response);
        $this->assertTrue(is_array($response->getErrors()));
        $this->assertEmpty($response->getErrors());
        $this->assertTrue($response->TransactionStatus);
    }

    public function testCreateTransactionWalletCaptureOn()
    {
        $walletId = 'VCOCallID:1234567890123456789';

        $payment = [
            'TotalAmount' => 1000,
        ];

        $transaction = [
            'Payment' => $payment,
            'Capture' => true,
            'TransactionType' => TransactionType::PURCHASE,
            'SecuredCardData' => $walletId,
        ];


        $mockTransaction = $transaction;
        $mockTransaction['Method'] = PaymentMethod::PROCESS_PAYMENT;

        $mockResponse = $this->getResponse([
            "AuthorisationCode" => "123456",
            "ResponseCode" => "00",
            "ResponseMessage" => "A2000",
            "TransactionID" => 12345678,
            "TransactionStatus" => true,
            "TransactionType" => "Recurring",
            "BeagleScore" => 0,
            "Verification" => [
                "CVN" => 0,
                "Address" => 0,
                "Email" => 0,
                "Mobile" => 0,
                "Phone" => 0,
            ],
            "Customer" => [
                "CardDetails" => [
                    "Number" => "444433XXXXXX1111",
                    "Name" => "Tung Ha",
                    "ExpiryMonth" => "12",
                    "ExpiryYear" => "21",
                    "StartMonth" => null,
                    "StartYear" => null,
                    "IssueNumber" => null,
                ],
                "TokenCustomerID" => null,
                "Reference" => "",
                "Title" => "Mr.",
                "FirstName" => "",
                "LastName" => "",
                "CompanyName" => "",
                "JobDescription" => "",
                "Street1" => "",
                "Street2" => "",
                "City" => "",
                "State" => "",
                "PostalCode" => "",
                "Country" => "",
                "Email" => "",
                "Phone" => "",
                "Mobile" => "",
                "Comments" => "",
                "Fax" => "",
                "Url" => "",
            ],
            "Payment" => [
                "TotalAmount" => 1000,
                "InvoiceNumber" => "",
                "InvoiceDescription" => "",
                "InvoiceReference" => "",
                "CurrencyCode" => "AUD",
            ],
            "Errors" => "",
        ]);

        $httpService = $this->prophet->prophesize('Eway\Rapid\Contract\HttpService');

        /** @var MethodProphecy $postTransactionStub */
        $postTransactionStub = $httpService->postTransaction(Argument::type('array'));
        $postTransactionStub->withArguments([$mockTransaction])->willReturn($mockResponse)->shouldBeCalled();

        /** @var HttpService $stub */
        $stub = $httpService->reveal();
        $this->client->setHttpService($stub);


        $response = $this->client->createTransaction(ApiMethod::WALLET, $transaction);

        $this->assertInstanceOf(CreateTransactionResponse::getClass(), $response);
        $this->assertTrue(is_array($response->getErrors()));
        $this->assertEmpty($response->getErrors());
        $this->assertTrue($response->TransactionStatus);
        $this->assertEquals($response->Payment->TotalAmount, $payment['TotalAmount']);
    }

    public function testCreateTransactionWalletCaptureOff()
    {
        $walletId = 'VCOCallID:1234567890123456789';

        $payment = [
            'TotalAmount' => 1000,
        ];

        $transaction = [
            'Payment' => $payment,
            'Capture' => false,
            'TransactionType' => TransactionType::PURCHASE,
            'SecuredCardData' => $walletId,
        ];


        $mockTransaction = $transaction;
        $mockTransaction['Method'] = PaymentMethod::AUTHORISE;

        $mockResponse = $this->getResponse([
            "AuthorisationCode" => "123456",
            "ResponseCode" => "00",
            "ResponseMessage" => "A2000",
            "TransactionID" => 12345678,
            "TransactionStatus" => true,
            "TransactionType" => "Recurring",
            "BeagleScore" => 0,
            "Verification" => [
                "CVN" => 0,
                "Address" => 0,
                "Email" => 0,
                "Mobile" => 0,
                "Phone" => 0,
            ],
            "Customer" => [
                "CardDetails" => [
                    "Number" => "444433XXXXXX1111",
                    "Name" => "Tung Ha",
                    "ExpiryMonth" => "12",
                    "ExpiryYear" => "21",
                    "StartMonth" => null,
                    "StartYear" => null,
                    "IssueNumber" => null,
                ],
                "TokenCustomerID" => null,
                "Reference" => "",
                "Title" => "Mr.",
                "FirstName" => "",
                "LastName" => "",
                "CompanyName" => "",
                "JobDescription" => "",
                "Street1" => "",
                "Street2" => "",
                "City" => "",
                "State" => "",
                "PostalCode" => "",
                "Country" => "",
                "Email" => "",
                "Phone" => "",
                "Mobile" => "",
                "Comments" => "",
                "Fax" => "",
                "Url" => "",
            ],
            "Payment" => [
                "TotalAmount" => 1000,
                "InvoiceNumber" => "",
                "InvoiceDescription" => "",
                "InvoiceReference" => "",
                "CurrencyCode" => "AUD",
            ],
            "Errors" => "",
        ]);

        $httpService = $this->prophet->prophesize('Eway\Rapid\Contract\HttpService');

        /** @var MethodProphecy $postTransactionStub */
        $postTransactionStub = $httpService->postTransaction(Argument::type('array'));
        $postTransactionStub->withArguments([$mockTransaction])->willReturn($mockResponse)->shouldBeCalled();

        /** @var HttpService $stub */
        $stub = $httpService->reveal();
        $this->client->setHttpService($stub);


        $response = $this->client->createTransaction(ApiMethod::WALLET, $transaction);

        $this->assertInstanceOf(CreateTransactionResponse::getClass(), $response);
        $this->assertTrue(is_array($response->getErrors()));
        $this->assertEmpty($response->getErrors());
        $this->assertTrue($response->TransactionStatus);
        $this->assertEquals($response->Payment->TotalAmount, $payment['TotalAmount']);
    }

    public function testCreateTransactionWithInvalidItems()
    {
        $customer = [
            'CardDetails' => [
                'Name' => 'John Smith',
                'Number' => '4444333322221111',
                'ExpiryMonth' => '12',
                'ExpiryYear' => '25',
                'CVN' => '123',
            ],
        ];
        $payment = [
            'TotalAmount' => 1000,
        ];
        $transaction = [
            'Customer' => $customer,
            'Payment' => $payment,
            'Items' => false,
            'Capture' => false,
            'TransactionType' => TransactionType::PURCHASE,
        ];


        $response = $this->client->createTransaction(ApiMethod::DIRECT, $transaction);
        $this->assertInstanceOf(CreateTransactionResponse::getClass(), $response);
        $this->assertTrue(is_array($response->getErrors()));
        $this->assertNotEmpty($response->getErrors());
        $this->assertContains(Client::ERROR_INVALID_ARGUMENT, $response->getErrors());
    }

    public function testCreateTransactionWithInvalidOptions()
    {
        $customer = [
            'CardDetails' => [
                'Name' => 'John Smith',
                'Number' => '4444333322221111',
                'ExpiryMonth' => '12',
                'ExpiryYear' => '25',
                'CVN' => '123',
            ],
        ];
        $payment = [
            'TotalAmount' => 1000,
        ];
        $transaction = [
            'Customer' => $customer,
            'Payment' => $payment,
            'Options' => false,
            'Capture' => false,
            'TransactionType' => TransactionType::PURCHASE,
        ];


        $response = $this->client->createTransaction(ApiMethod::DIRECT, $transaction);
        $this->assertInstanceOf(CreateTransactionResponse::getClass(), $response);
        $this->assertTrue(is_array($response->getErrors()));
        $this->assertNotEmpty($response->getErrors());
        $this->assertContains(Client::ERROR_INVALID_ARGUMENT, $response->getErrors());
    }
}
