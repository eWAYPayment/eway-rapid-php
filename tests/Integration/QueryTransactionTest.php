<?php

namespace Eway\Test\Integration;

use Eway\Rapid\Enum\ApiMethod;
use Eway\Rapid\Enum\TransactionType;
use Eway\Rapid\Model\Response\QueryTransactionResponse;
use Eway\Test\AbstractClientTest;

/**
 * Class QueryTransactionTest.
 */
class QueryTransactionTest extends AbstractClientTest
{
    /**
     * Test query transaction using Transaction ID
     */
    public function testQueryTransactionWithTransactionId()
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

        $transactionId = $createTransactionResponse->TransactionID;
        $response = $this->client->queryTransaction($transactionId);
        $this->assertInstanceOf(QueryTransactionResponse::getClass(), $response);
        $this->assertTrue(is_array($response->getErrors()));
        $this->assertEmpty($response->getErrors());
        $this->assertTrue(is_array($response->Transactions));
        $this->assertTrue(count($response->Transactions) > 0);
        $this->assertEquals($createTransactionResponse->TransactionID, $response->Transactions[0]->TransactionID);
    }

    /**
     * Test query transaction using Access Code
     */
    public function testQueryTransactionWithAccessCode()
    {
        $payment = [
            "TotalAmount" => 100,
        ];

        $transaction = [
            "Payment" => $payment,
            "RedirectUrl" => "http://www.eway.com.au",
            "CancelUrl" => "http://www.eway.com.au",
            "TransactionType" => TransactionType::PURCHASE,
            'Capture' => true,
        ];

        $createTransactionResponse = $this->client->createTransaction(ApiMethod::RESPONSIVE_SHARED, $transaction);

        $accessCode = $createTransactionResponse->AccessCode;
        $response = $this->client->queryTransaction($accessCode);
        $this->assertInstanceOf(QueryTransactionResponse::getClass(), $response);
        $this->assertTrue(is_array($response->getErrors()));
        $this->assertEmpty($response->getErrors());
        $this->assertTrue(is_array($response->Transactions));
        $this->assertTrue(count($response->Transactions) > 0);
    }

    /**
     * Test query transaction using transaction filter Invoice Number
     */
    public function testQueryTransactionFilterWithInvoiceNumber()
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
            'InvoiceNumber' => 'invoice_number_'.rand(),
        ];
        $transaction = [
            'Customer' => $customer,
            'Payment' => $payment,
            'Capture' => true,
            'TransactionType' => TransactionType::PURCHASE,
        ];
        $createTransactionResponse = $this->client->createTransaction(ApiMethod::DIRECT, $transaction);

        $response = $this->client->queryInvoiceNumber($payment['InvoiceNumber']);
        $this->assertInstanceOf(QueryTransactionResponse::getClass(), $response);
        $this->assertTrue(is_array($response->getErrors()));
        $this->assertEmpty($response->getErrors());
        $this->assertTrue(is_array($response->Transactions));
        $this->assertTrue(count($response->Transactions) > 0);
        $this->assertEquals($createTransactionResponse->TransactionID, $response->Transactions[0]->TransactionID);
        $this->assertEquals($payment['InvoiceNumber'], $response->Transactions[0]->InvoiceNumber);
    }

    /**
     * Test query transaction using transaction filter Invoice Reference
     */
    public function testQueryTransactionFilterWithInvoiceReference()
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
            'InvoiceReference' => rand(),
        ];
        $transaction = [
            'Customer' => $customer,
            'Payment' => $payment,
            'Capture' => true,
            'TransactionType' => TransactionType::PURCHASE,
        ];
        $createTransactionResponse = $this->client->createTransaction(ApiMethod::DIRECT, $transaction);

        $response = $this->client->queryInvoiceReference($payment['InvoiceReference']);
        $this->assertInstanceOf(QueryTransactionResponse::getClass(), $response);
        $this->assertTrue(is_array($response->getErrors()));
        $this->assertEmpty($response->getErrors());
        $this->assertTrue(is_array($response->Transactions));
        $this->assertTrue(count($response->Transactions) > 0);
        $this->assertEquals($createTransactionResponse->TransactionID, $response->Transactions[0]->TransactionID);
        $this->assertEquals($payment['InvoiceReference'], $response->Transactions[0]->InvoiceReference);
    }
}
