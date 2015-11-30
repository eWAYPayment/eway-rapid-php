<?php

namespace Eway\Test\Unit\Client;

use Eway\Rapid\Client;
use Eway\Rapid\Contract\Client as ClientContract;
use Eway\Rapid\Contract\HttpService;
use Eway\Rapid\Enum\ApiMethod;
use Eway\Rapid\Enum\PaymentMethod;
use Eway\Rapid\Enum\TransactionType;
use Eway\Rapid\Model\Response\AbstractResponse;
use Eway\Rapid\Model\Response\CreateTransactionResponse;
use Eway\Rapid\Model\Response\QueryAccessCodeResponse;
use Eway\Rapid\Service\Http\Response;
use Eway\Test\AbstractClientTest;
use Prophecy\Argument;
use Prophecy\Prophecy\MethodProphecy;

/**
 * Class ClientTest.
 */
class ClientTest extends AbstractClientTest
{

    /**
     * @dataProvider \Eway\Test\Unit\Client\ClientTest::provideEndpoint
     *
     * @param $endpoint
     * @param $expected
     */
    public function testEndpoint($endpoint, $expected)
    {
        $this->client->setEndpoint($endpoint);
        $this->assertEquals($expected, $this->client->getEndpoint());
        $this->assertEquals($expected, $this->client->getHttpService()->getBaseUrl());
    }

    /**
     * @dataProvider \Eway\Test\Unit\Client\ClientTest::provideCredentials
     *
     * @param $key
     * @param $password
     */
    public function testSetCredentials($key, $password)
    {
        $this->client->setCredential($key, $password);
        $this->assertEquals($key, $this->client->getHttpService()->getKey());
        $this->assertEquals($password, $this->client->getHttpService()->getPassword());
    }

    public function testInvokeInvalidMethod()
    {
        $response = $this->client->createTransaction('foo', []);

        $this->assertInstanceOf(CreateTransactionResponse::getClass(), $response);
        $this->assertTrue(is_array($response->getErrors()));
        $this->assertNotEmpty($response->getErrors());
        $this->assertContains(ClientContract::ERROR_INVALID_ARGUMENT, $response->getErrors());
    }

    public function testInvokeInvalidData()
    {
        $response = $this->client->createTransaction(ApiMethod::DIRECT, ['bar' => 'baz']);

        $this->assertInstanceOf(CreateTransactionResponse::getClass(), $response);
        $this->assertTrue(is_array($response->getErrors()));
        $this->assertNotEmpty($response->getErrors());
        $this->assertContains(ClientContract::ERROR_INVALID_ARGUMENT, $response->getErrors());
    }

    public function testInvalidJsonResponse()
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

        $mockResponse = $this->getResponse('foo');

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
        $this->assertNotEmpty($response->getErrors());
        $this->assertContains(ClientContract::ERROR_INVALID_JSON, $response->getErrors());
    }

    public function testEmptyResponse()
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

        $httpService = $this->prophet->prophesize('Eway\Rapid\Contract\HttpService');

        /** @var MethodProphecy $postTransactionStub */
        $postTransactionStub = $httpService->postTransaction(Argument::type('array'));
        $postTransactionStub->withArguments([$mockTransaction])->willReturn(null)->shouldBeCalled();

        /** @var HttpService $stub */
        $stub = $httpService->reveal();
        $this->client->setHttpService($stub);


        $response = $this->client->createTransaction(ApiMethod::DIRECT, $transaction);

        $this->assertInstanceOf(CreateTransactionResponse::getClass(), $response);
        $this->assertTrue(is_array($response->getErrors()));
        $this->assertNotEmpty($response->getErrors());
        $this->assertContains(ClientContract::ERROR_EMPTY_RESPONSE, $response->getErrors());
    }

    public function testClientErrorResponse()
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

        $mockResponse = new Response(404);

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
        $this->assertNotEmpty($response->getErrors());
        $this->assertCount(1, $response->getErrors());
        $this->assertContains(ClientContract::ERROR_HTTP_AUTHENTICATION_ERROR, $response->getErrors());
    }

    public function testServerErrorResponse()
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

        $mockResponse = new Response(503);

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
        $this->assertNotEmpty($response->getErrors());
        $this->assertCount(1, $response->getErrors());
        $this->assertContains(ClientContract::ERROR_HTTP_SERVER_ERROR, $response->getErrors());
    }

    public function testQueryAccessCode()
    {
        $accessCode = 'A10012yIV2-MEEfkk7b7oYZqtulwNHv2dAFLv7T2guZEpjwBMHJoU-KxQihXVV10unFYbOUJ9Ob58oALLxn88_rzWDJhyq1-qW_hZ-xYjS3kdsCSNLtFHVESfDRVPWZqisLto';

        $mockResponse = $this->getResponse([
            'AccessCode' => $accessCode,
            'AuthorisationCode' => null,
            'ResponseCode' => null,
            'ResponseMessage' => '',
            'InvoiceNumber' => '',
            'InvoiceReference' => '',
            'TotalAmount' => 0,
            'TransactionID' => null,
            'TransactionStatus' => false,
            'TokenCustomerID' => null,
            'BeagleScore' => null,
            'Options' => [],
            'Verification' => [
                'CVN' => 0,
                'Address' => 0,
                'Email' => 0,
                'Mobile' => 0,
                'Phone' => 0,
            ],
            'BeagleVerification' => [
                'Email' => 0,
                'Phone' => 0,
            ],
            'Errors' => null,
        ]);

        $httpService = $this->prophet->prophesize('Eway\Rapid\Contract\HttpService');

        /** @var MethodProphecy $getAccessCodeStub */
        $getAccessCodeStub = $httpService->getAccessCode(Argument::type('string'));
        $getAccessCodeStub->withArguments([$accessCode])->willReturn($mockResponse)->shouldBeCalled();

        /** @var HttpService $stub */
        $stub = $httpService->reveal();
        $this->client->setHttpService($stub);


        $response = $this->client->queryAccessCode($accessCode);

        $this->assertInstanceOf(QueryAccessCodeResponse::getClass(), $response);
        $this->assertTrue(is_array($response->getErrors()));
        $this->assertEmpty($response->getErrors());
    }

    public function testInvalidConstructorData()
    {
        $client = new Client('', '', '');
        $response = $client->queryTransaction('foo');
        $this->assertInstanceOf(AbstractResponse::getClass(), $response);
        $this->assertTrue(is_array($response->getErrors()));
        $this->assertNotEmpty($response->getErrors());
        $this->assertContains(ClientContract::ERROR_INVALID_CREDENTIAL, $response->getErrors());
        $this->assertContains(ClientContract::ERROR_INVALID_ENDPOINT, $response->getErrors());
    }

    /**
     * @return array
     */
    public function provideEndpoint()
    {
        return [
            ['sandbox', Client::ENDPOINT_SANDBOX],
            ['sAnDbOx', Client::ENDPOINT_SANDBOX],
            ['production', Client::ENDPOINT_PRODUCTION],
            ['pRoDuCtIoN', Client::ENDPOINT_PRODUCTION],
            ['https://www.google.com', 'https://www.google.com'],
        ];
    }

    /**
     * @return array
     */
    public function provideCredentials()
    {
        return [
            [null, null],
            ['', ''],
            ['foo', ''],
            ['', 'bar'],
            ['foo', 'bar'],
        ];
    }
}
