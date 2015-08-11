<?php

namespace Eway\Test\Integration;

use Eway\Rapid\Enum\ApiMethod;
use Eway\Rapid\Enum\TransactionType;
use Eway\Rapid\Model\Response\RefundResponse;
use Eway\Test\AbstractClientTest;

/**
 * Class CancelTransactionTest
 */
class CancelTransactionTest extends AbstractClientTest
{
    /**
     * Cancel Authorised Transaction
     */
    public function testCancelTransaction()
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
        $authResponse = $this->client->createTransaction(ApiMethod::DIRECT, $transaction);

        $transactionId = $authResponse->TransactionID;
        $response = $this->client->cancelTransaction($transactionId);
        $this->assertInstanceOf(RefundResponse::getClass(), $response);
        $this->assertTrue(is_array($response->getErrors()));
        $this->assertEmpty($response->getErrors());
        $this->assertTrue($response->TransactionStatus);
    }
}
