<?php

namespace Eway\Test\Unit\Client;

use Eway\Rapid\Contract\Client;
use Eway\Rapid\Contract\HttpService;
use Eway\Rapid\Model\Response\QueryTransactionResponse;
use Eway\Test\AbstractClientTest;
use Prophecy\Argument;
use Prophecy\Prophecy\MethodProphecy;

/**
 * Class QueryTransactionTest.
 */
class QueryTransactionTest extends AbstractClientTest
{
    /**
     * Test query transaction using Transaction ID/Access Code
     */
    public function testQueryTransactionShouldCallHttpServiceGetTransaction()
    {
        $reference = 12345678;


        $mockResponse = $this->getResponse([
            'Transactions' => [
                [
                    'AuthorisationCode' => '123456',
                    'ResponseCode' => '00',
                    'ResponseMessage' => 'A2000',
                    'InvoiceNumber' => '',
                    'InvoiceReference' => '',
                    'TotalAmount' => 1000,
                    'TransactionID' => $reference,
                    'TransactionStatus' => true,
                    'TokenCustomerID' => null,
                    'BeagleScore' => 0,
                    'Options' => [
                    ],
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
                    'Customer' => [
                        'TokenCustomerID' => null,
                        'Reference' => null,
                        'Title' => null,
                        'FirstName' => '',
                        'LastName' => '',
                        'CompanyName' => null,
                        'JobDescription' => null,
                        'Street1' => '',
                        'Street2' => '',
                        'City' => '',
                        'State' => '',
                        'PostalCode' => '',
                        'Country' => '',
                        'Email' => '',
                        'Phone' => '',
                        'Mobile' => null,
                        'Comments' => null,
                        'Fax' => null,
                        'Url' => null,
                    ],
                    'CustomerNote' => null,
                    'ShippingAddress' => [
                        'ShippingMethod' => 'Unknown',
                        'FirstName' => '',
                        'LastName' => '',
                        'Street1' => '',
                        'Street2' => '',
                        'City' => '',
                        'State' => '',
                        'Country' => '',
                        'PostalCode' => '',
                        'Email' => '',
                        'Phone' => '',
                        'Fax' => null,
                    ],
                ],
            ],
            'Errors' => '',
        ]);

        $httpService = $this->prophet->prophesize('Eway\Rapid\Contract\HttpService');

        /** @var MethodProphecy $getTransactionStub */
        $getTransactionStub = $httpService->getTransaction(Argument::exact($reference));
        $getTransactionStub->withArguments([$reference])->willReturn($mockResponse)->shouldBeCalled();

        /** @var HttpService $stub */
        $stub = $httpService->reveal();
        $this->client->setHttpService($stub);


        $response = $this->client->queryTransaction($reference);

        $this->assertInstanceOf(QueryTransactionResponse::getClass(), $response);
        $this->assertTrue(is_array($response->getErrors()));
        $this->assertEmpty($response->getErrors());
        $this->assertTrue(is_array($response->Transactions));
        $this->assertTrue(count($response->Transactions) > 0);
        $this->assertEquals($reference, $response->Transactions[0]->TransactionID);
    }

    /**
     * Test query transaction using transaction filter Invoice Number
     */
    public function testQueryInvoiceNumberShouldCallHttpServiceGetTransactionInvoiceNumber()
    {
        $transactionId = 12345678;
        $invoiceNumber = 'invoice_number_1234567890';


        $mockResponse = $this->getResponse([
            'Transactions' => [
                [
                    'AuthorisationCode' => '123456',
                    'ResponseCode' => '00',
                    'ResponseMessage' => 'A2000',
                    'InvoiceNumber' => 'invoice_number_1234567890',
                    'InvoiceReference' => '',
                    'TotalAmount' => 1000,
                    'TransactionID' => $transactionId,
                    'TransactionStatus' => true,
                    'TokenCustomerID' => null,
                    'BeagleScore' => 0,
                    'Options' => [
                    ],
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
                    'Customer' => [
                        'TokenCustomerID' => null,
                        'Reference' => null,
                        'Title' => null,
                        'FirstName' => '',
                        'LastName' => '',
                        'CompanyName' => null,
                        'JobDescription' => null,
                        'Street1' => '',
                        'Street2' => '',
                        'City' => '',
                        'State' => '',
                        'PostalCode' => '',
                        'Country' => '',
                        'Email' => '',
                        'Phone' => '',
                        'Mobile' => null,
                        'Comments' => null,
                        'Fax' => null,
                        'Url' => null,
                    ],
                    'CustomerNote' => null,
                    'ShippingAddress' => [
                        'ShippingMethod' => 'Unknown',
                        'FirstName' => '',
                        'LastName' => '',
                        'Street1' => '',
                        'Street2' => '',
                        'City' => '',
                        'State' => '',
                        'Country' => '',
                        'PostalCode' => '',
                        'Email' => '',
                        'Phone' => '',
                        'Fax' => null,
                    ],
                ],
            ],
            'Errors' => '',
        ]);

        $httpService = $this->prophet->prophesize('Eway\Rapid\Contract\HttpService');

        /** @var MethodProphecy $getTransactionInvoiceNumberStub */
        $getTransactionInvoiceNumberStub = $httpService->getTransactionInvoiceNumber(Argument::exact($invoiceNumber));
        $getTransactionInvoiceNumberStub->withArguments([$invoiceNumber])->willReturn($mockResponse)->shouldBeCalled();

        /** @var HttpService $stub */
        $stub = $httpService->reveal();
        $this->client->setHttpService($stub);


        $response = $this->client->queryInvoiceNumber($invoiceNumber);

        $this->assertInstanceOf(QueryTransactionResponse::getClass(), $response);
        $this->assertTrue(is_array($response->getErrors()));
        $this->assertEmpty($response->getErrors());
        $this->assertTrue(is_array($response->Transactions));
        $this->assertTrue(count($response->Transactions) > 0);
        $this->assertEquals($transactionId, $response->Transactions[0]->TransactionID);
        $this->assertEquals($invoiceNumber, $response->Transactions[0]->InvoiceNumber);
    }

    /**
     * Test query transaction using transaction filter Invoice Reference
     */
    public function testQueryInvoiceReferenceShouldCallHttpServiceGetTransactionInvoiceReference()
    {
        $transactionId = 12345678;
        $invoiceReference = '1234567890';


        $mockResponse = $this->getResponse([
            'Transactions' => [
                [
                    'AuthorisationCode' => '123456',
                    'ResponseCode' => '00',
                    'ResponseMessage' => 'A2000',
                    'InvoiceNumber' => '',
                    'InvoiceReference' => $invoiceReference,
                    'TotalAmount' => 1000,
                    'TransactionID' => $transactionId,
                    'TransactionStatus' => true,
                    'TokenCustomerID' => null,
                    'BeagleScore' => 0,
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
                    'Customer' => [
                        'TokenCustomerID' => null,
                        'Reference' => null,
                        'Title' => null,
                        'FirstName' => '',
                        'LastName' => '',
                        'CompanyName' => null,
                        'JobDescription' => null,
                        'Street1' => '',
                        'Street2' => '',
                        'City' => '',
                        'State' => '',
                        'PostalCode' => '',
                        'Country' => '',
                        'Email' => '',
                        'Phone' => '',
                        'Mobile' => null,
                        'Comments' => null,
                        'Fax' => null,
                        'Url' => null,
                    ],
                    'CustomerNote' => null,
                    'ShippingAddress' => [
                        'ShippingMethod' => 'Unknown',
                        'FirstName' => '',
                        'LastName' => '',
                        'Street1' => '',
                        'Street2' => '',
                        'City' => '',
                        'State' => '',
                        'Country' => '',
                        'PostalCode' => '',
                        'Email' => '',
                        'Phone' => '',
                        'Fax' => null,
                    ],
                ],
            ],
            'Errors' => '',
        ]);

        $httpService = $this->prophet->prophesize('Eway\Rapid\Contract\HttpService');

        /** @var MethodProphecy $getTransactionInvoiceReference */
        $getTransactionInvoiceReference = $httpService->getTransactionInvoiceReference(Argument::exact($invoiceReference));
        $getTransactionInvoiceReference->willReturn($mockResponse)->shouldBeCalled();

        /** @var HttpService $stub */
        $stub = $httpService->reveal();
        $this->client->setHttpService($stub);


        $response = $this->client->queryInvoiceReference($invoiceReference);

        $this->assertInstanceOf(QueryTransactionResponse::getClass(), $response);
        $this->assertTrue(is_array($response->getErrors()));
        $this->assertEmpty($response->getErrors());
        $this->assertTrue(is_array($response->Transactions));
        $this->assertTrue(count($response->Transactions) > 0);
        $this->assertEquals($transactionId, $response->Transactions[0]->TransactionID);
        $this->assertEquals($invoiceReference, $response->Transactions[0]->InvoiceReference);
    }

    /**
     * Test query transaction using Transaction ID/Access Code
     */
    public function testQueryTransactionReturnInvalidResponse()
    {
        $reference = 12345678;


        $mockResponse = $this->getResponse([
            'Transactions' => false,
            'Errors' => '',
        ]);

        $httpService = $this->prophet->prophesize('Eway\Rapid\Contract\HttpService');

        /** @var MethodProphecy $getTransactionStub */
        $getTransactionStub = $httpService->getTransaction(Argument::exact($reference));
        $getTransactionStub->withArguments([$reference])->willReturn($mockResponse)->shouldBeCalled();

        /** @var HttpService $stub */
        $stub = $httpService->reveal();
        $this->client->setHttpService($stub);


        $response = $this->client->queryTransaction($reference);

        $this->assertInstanceOf(QueryTransactionResponse::getClass(), $response);
        $this->assertTrue(is_array($response->getErrors()));
        $this->assertNotEmpty($response->getErrors());
        $this->assertContains(Client::ERROR_INVALID_ARGUMENT, $response->getErrors());
    }
}
