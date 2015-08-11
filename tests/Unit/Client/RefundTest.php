<?php

namespace Eway\Test\Unit\Client;

use Eway\Rapid\Contract\HttpService;
use Eway\Rapid\Model\Response\RefundResponse;
use Eway\Test\AbstractClientTest;
use Prophecy\Argument;
use Prophecy\Prophecy\MethodProphecy;

/**
 * Class RefundTest.
 */
class RefundTest extends AbstractClientTest
{
    /**
     * Test refund a transaction
     */
    public function testRefundShouldCallHttpServicePostTransactionRefund()
    {
        $transactionId = 12345678;
        $refund = [
            'Refund' => [
                'TransactionID' => $transactionId,
                'TotalAmount' => 1000,
            ],
        ];


        $mockResponse = $this->getResponse([
            'AuthorisationCode' => '123456',
            'ResponseCode' => null,
            'ResponseMessage' => 'A2000',
            'TransactionID' => 12345679,
            'TransactionStatus' => true,
            'Verification' => null,
            'Customer' => [
                'CardDetails' => [
                    'Number' => null,
                    'Name' => null,
                    'ExpiryMonth' => null,
                    'ExpiryYear' => null,
                    'StartMonth' => null,
                    'StartYear' => null,
                    'IssueNumber' => null,
                ],
                'TokenCustomerID' => null,
                'Reference' => null,
                'Title' => null,
                'FirstName' => '',
                'LastName' => '',
                'CompanyName' => null,
                'JobDescription' => null,
                'Street1' => '',
                'Street2' => null,
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
            'Refund' => [
                'TransactionID' => $transactionId,
                'TotalAmount' => 1000,
                'InvoiceNumber' => null,
                'InvoiceDescription' => '',
                'InvoiceReference' => '',
                'CurrencyCode' => null,
            ],
            'Errors' => '',
        ]);

        $httpService = $this->prophet->prophesize('Eway\Rapid\Contract\HttpService');

        /** @var MethodProphecy $postTransactionRefundStub */
        $postTransactionRefundStub = $httpService->postTransactionRefund(
            Argument::exact($transactionId),
            Argument::type('array')
        );
        $postTransactionRefundStub->withArguments([$transactionId, $refund])->willReturn($mockResponse)->shouldBeCalled();

        /** @var HttpService $stub */
        $stub = $httpService->reveal();
        $this->client->setHttpService($stub);


        $response = $this->client->refund($refund);

        $this->assertInstanceOf(RefundResponse::getClass(), $response);
        $this->assertTrue(is_array($response->getErrors()));
        $this->assertEmpty($response->getErrors());
        $this->assertTrue($response->TransactionStatus);
        foreach ($refund['Refund'] as $key => $value) {
            $this->assertEquals($value, $response->Refund->$key);
        }
    }
}
