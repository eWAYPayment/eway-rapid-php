<?php

namespace Eway\Test\Unit\Client;

use Eway\Rapid\Contract\HttpService;
use Eway\Rapid\Enum\ApiMethod;
use Eway\Rapid\Enum\PaymentMethod;
use Eway\Rapid\Enum\TransactionType;
use Eway\Rapid\Model\Response\CreateCustomerResponse;
use Eway\Test\AbstractClientTest;
use Prophecy\Argument;
use Prophecy\Prophecy\MethodProphecy;

/**
 * Class CreateCustomerTest.
 */
class CreateCustomerTest extends AbstractClientTest
{
    /**
     * Create Customer Token for Direct Connection Transaction
     */
    public function testCreateCustomerDirectConnectionShouldCallHttpServicePostTransaction()
    {
        $customer = [
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

        $transaction = [
            'Customer' => $customer,
            'Method' => PaymentMethod::CREATE_TOKEN_CUSTOMER,
            'TransactionType' => TransactionType::PURCHASE,
        ];

        $mockTransaction = $transaction;
        $mockTransaction['Capture'] = true;

        $mockResponse = $this->getResponse([
            'AuthorisationCode' => null,
            'ResponseCode' => '00',
            'ResponseMessage' => 'A2000',
            'TransactionID' => null,
            'TransactionStatus' => false,
            'TransactionType' => 'Purchase',
            'BeagleScore' => null,
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
                'TokenCustomerID' => 987654321098,
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
                'TotalAmount' => 0,
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


        $response = $this->client->createCustomer(ApiMethod::DIRECT, $customer);

        $this->assertInstanceOf(CreateCustomerResponse::getClass(), $response);
        $this->assertTrue(is_array($response->getErrors()));
        $this->assertEmpty($response->getErrors());
        $this->assertCustomer($customer, $response);
        $this->assertNotEmpty($response->Customer->TokenCustomerID);
    }

    /**
     * Create Customer Token for Responsive Shared Transaction
     */
    public function testCreateCustomerResponsiveSharedShouldCallHttpServicePostAccessCodeShared()
    {
        $customer = [
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
            "RedirectUrl" => "http://www.eway.com.au",
            "CancelUrl" => "http://www.eway.com.au",
        ];

        $transaction = [
            'Customer' => $customer,
            'Method' => PaymentMethod::CREATE_TOKEN_CUSTOMER,
            'TransactionType' => TransactionType::PURCHASE,
            'RedirectUrl' => $customer['RedirectUrl'],
            'CancelUrl' => $customer['CancelUrl'],
            'Payment' => [
                'TotalAmount' => 0,
            ],
        ];

        $mockTransaction = $transaction;
        $mockTransaction['Capture'] = true;

        $accessCode = 'A1001mNKkLhmciCXSW-52SoNuoGWs4OEq0G88LRzgYhJsE3za9LiOtiCoDeR7BUZKPwBkLXe_ETrYUhOChs05mw23q9iXt2EUlSqj1OpoAaSNk6AjZjDdxwl1Ze4PXI337860';
        $mockResponse = $this->getResponse([
            'SharedPaymentUrl' => 'https://secure-au.sandbox.ewaypayments.com/sharedpage/sharedpayment?AccessCode='.$accessCode,
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
                'TotalAmount' => 0,
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

        /** @var MethodProphecy $postAccessCodeSharedStub */
        $postAccessCodeSharedStub = $httpService->postAccessCodeShared(Argument::type('array'));
        $postAccessCodeSharedStub->withArguments([$mockTransaction])->willReturn($mockResponse)->shouldBeCalled();

        /** @var HttpService $stub */
        $stub = $httpService->reveal();
        $this->client->setHttpService($stub);


        $response = $this->client->createCustomer(ApiMethod::RESPONSIVE_SHARED, $customer);

        $this->assertInstanceOf(CreateCustomerResponse::getClass(), $response);
        $this->assertTrue(is_array($response->getErrors()));
        $this->assertEmpty($response->getErrors());
        $this->assertEquals($accessCode, $response->AccessCode);
    }

    /**
     * Create Customer Token for Transparent Redirect Transaction
     */
    public function testCreateCustomerTransparentRedirectShouldCallHttpServicePostAccessCode()
    {
        $customer = [
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
            "RedirectUrl" => "http://www.eway.com.au",
        ];

        $transaction = [
            'Customer' => $customer,
            'Method' => PaymentMethod::CREATE_TOKEN_CUSTOMER,
            'TransactionType' => TransactionType::PURCHASE,
            'RedirectUrl' => $customer['RedirectUrl'],
            'Payment' => [
                'TotalAmount' => 0,
            ],
        ];

        $mockTransaction = $transaction;
        $mockTransaction['Capture'] = true;

        $accessCode = '44DD7HSnuWszo4FHAYyV2hOoUPKfGvK2J6jSEcNJarBBSu7H6Jne994F9P9Kdko2iJfqXzwt-Eld7y2vOadjc0b6XvX_pwYRPhqaU2MiM1LeKGacHGXZoF_KMJGmYGg4qnVS7';
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
                'TotalAmount' => 0,
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


        $response = $this->client->createCustomer(ApiMethod::TRANSPARENT_REDIRECT, $customer);

        $this->assertInstanceOf(CreateCustomerResponse::getClass(), $response);
        $this->assertTrue(is_array($response->getErrors()));
        $this->assertEmpty($response->getErrors());
        $this->assertEquals($accessCode, $response->AccessCode);
    }
}
