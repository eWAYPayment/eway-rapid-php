<?php

namespace Eway\Test\Integration;

use Eway\Rapid\Enum\ApiMethod;
use Eway\Rapid\Enum\TransactionType;
use Eway\Rapid\Model\Response\RefundResponse;
use Eway\Test\AbstractClientTest;

/**
 * Class RefundTest.
 */
class RefundTest extends AbstractClientTest
{
    /**
     * Test refund a transaction
     */
    public function testRefund()
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

        $createTransactionResponse = $this->client->createTransaction(ApiMethod::DIRECT, $transaction);

        $refundDetails = $payment;
        $refundDetails['TransactionID'] = $createTransactionResponse->TransactionID;
        $refund = [
            'Refund' => $refundDetails,
        ];

        $response = $this->client->refund($refund);

        $this->assertInstanceOf(RefundResponse::getClass(), $response);
        $this->assertTrue(is_array($response->getErrors()));
        $this->assertEmpty($response->getErrors());
        $this->assertTrue($response->TransactionStatus);
        foreach ($refundDetails as $key => $value) {
            $this->assertEquals($value, $response->Refund->$key);
        }
    }
}
