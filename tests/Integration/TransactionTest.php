<?php

declare(strict_types=1);

namespace Eway\Test\Integration;

use Eway\Rapid\Enum\ApiMethod;
use Eway\Rapid\Enum\ShippingMethod;
use Eway\Rapid\Enum\TransactionType;
use Eway\Rapid\Model\Response\CreateTransactionResponse;
use Eway\Rapid\Model\Response\QueryAccessCodeResponse;
use Eway\Rapid\Model\Response\QueryTransactionResponse;
use Eway\Rapid\Model\Transaction;

class TransactionTest extends AbstractTestCase
{
    /** @var array $responseData */
    private array $responseData = [
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
            ],
            'TokenCustomerID' => 'token',
            'Title' => 'Mr.',
        ],
        'Payment' => [
            'TotalAmount' => 1000,
            'InvoiceNumber' => '',
            'InvoiceDescription' => '',
            'InvoiceReference' => '',
            'CurrencyCode' => 'AUD',
        ],
        'Errors' => '',
    ];

    /**
     * @dataProvider createTransactionDataProvider
     * @param string $apiMethod
     * @param bool $capture
     * @param string|null $shippingMethod
     * @return void
     */
    public function testCreateTransaction(
        string $apiMethod,
        bool $capture,
        ?string $shippingMethod,
        ?string $token
    ): void {
        $transaction = [
            'Customer' => [
                'TokenCustomerID' => $token,
                'Reference' => 'A12345',
                'Title' => 'Mr.',
                'FirstName' => 'John',
                'LastName' => 'Smith',
                'CompanyName' => 'Demo Shop 123',
                'JobDescription' => 'Developer',
                'Street1' => 'Level 5',
                'Street2' => '369 Queen Street',
                'City' => 'Sydney',
                'State' => 'NSW',
                'PostalCode' => '2000',
                'Country' => 'au',
                'Phone' => '09 889 0986',
                'Mobile' => '09 889 6542',
                'Email' => 'demo@example.org',
                'Url' => 'https://www.ewaypayments.com',
                'CardDetails' => [
                    'Name' => 'John Smith',
                    'Number' => '4444333322221111',
                    'ExpiryMonth' => '12',
                    'ExpiryYear' => '25',
                    'StartMonth' => '01',
                    'StartYear' => '13',
                    'IssueNumber' => '01',
                    'CVN' => '123',
                ],
            ],
            'ShippingAddress' => [
                'ShippingMethod' => $shippingMethod,
                'FirstName' => 'John',
                'LastName' => 'Smith',
                'Street1' => 'Level 5',
                'Street2' => '369 Queen Street',
                'City' => 'Sydney',
                'State' => 'NSW',
                'Country' => 'au',
                'PostalCode' => '2000',
                'Phone' => '09 889 0986',
            ],
            'Items' => [
                [
                    'SKU' => '12345678901234567890',
                    'Description' => 'Item Description 1',
                    'Quantity' => 1,
                    'UnitCost' => 400,
                    'Tax' => 100,
                ],
                [
                    'SKU' => '123456789012',
                    'Description' => 'Item Description 2',
                    'Quantity' => 1,
                    'UnitCost' => 400,
                    'Tax' => 100,
                ],
                [
                    'SKU' => '123456789015',
                    'Description' => 'Item Description 3',
                    'Quantity' => 2,
                    'UnitCost' => 150,
                ],
            ],
            'Options' => [
                ['Value' => 'Option1'],
                ['Value' => 'Option2'],
            ],
            'Payment' => [
                'TotalAmount' => 1000,
                'InvoiceNumber' => 'Inv 21540',
                'InvoiceDescription' => 'Individual Invoice Description',
                'InvoiceReference' => '513456',
                'CurrencyCode' => 'AUD',
            ],
            'DeviceID' => 'D1234',
            'CustomerIP' => '127.0.0.1',
            'PartnerID' => 'ID',
            'TransactionType' => TransactionType::PURCHASE,
            'Capture' => $capture,
        ];

        $this->setUpCurl(200, $this->responseData);
        $response = $this->client->createTransaction($apiMethod, $transaction);
        $this->assertInstanceOf(CreateTransactionResponse::class, $response);
        $this->assertIsArray($response->getErrors());
        $this->assertEmpty($response->getErrors());
        $this->assertEquals($this->responseData, $response->toArray());
    }

    /**
     * @return array[]
     */
    public function createTransactionDataProvider(): array
    {
        return [
            [ApiMethod::DIRECT, false, null, null],
            [ApiMethod::DIRECT, true, ShippingMethod::NEXT_DAY, null],
            [ApiMethod::WALLET, false, ShippingMethod::UNKNOWN, 'token'],
            [ApiMethod::WALLET, true, ShippingMethod::LOW_COST, null],
            [ApiMethod::RESPONSIVE_SHARED, false, ShippingMethod::INTERNATIONAL, null],
            [ApiMethod::RESPONSIVE_SHARED, true, ShippingMethod::DESIGNATED_BY_CUSTOMER, null],
            [ApiMethod::RESPONSIVE_SHARED, true, ShippingMethod::TWO_DAY_SERVICE, 'token'],
            [ApiMethod::TRANSPARENT_REDIRECT, false, ShippingMethod::MILITARY, 'token'],
            [ApiMethod::TRANSPARENT_REDIRECT, true, ShippingMethod::OTHER, null],
            [ApiMethod::TRANSPARENT_REDIRECT, true, ShippingMethod::STORE_PICKUP, 'token'],
            [ApiMethod::AUTHORISATION, false, ShippingMethod::STORE_PICKUP, null],
            [ApiMethod::AUTHORISATION, true, ShippingMethod::THREE_DAY_SERVICE, 'token'],
        ];
    }

    /**
     * @return void
     */
    public function testQueryTransaction(): void
    {
        $this->setUpCurl(200, ['Transactions' => [$this->responseData]]);
        $response = $this->client->queryTransaction('reference');
        $this->assertInstanceOf(QueryTransactionResponse::class, $response);
        $this->assertIsArray($response->getErrors());
        $this->assertEmpty($response->getErrors());
        $this->assertEquals([new Transaction($this->responseData)], $response->Transactions);
    }

    /**
     * @return void
     */
    public function testQueryAccessCode(): void
    {
        $accessCode = '123456';
        $data = "HTTP/1.0 200 Connection established\r\n\r\n" . json_encode(['AccessCode' => $accessCode]);

        $this->setUpCurl(200, $data);
        $this->curl->expects($this->once())->method('getVersion')->willReturn(['version_number' => 468480]);
        $response = $this->client->queryAccessCode($accessCode);

        $this->assertInstanceOf(QueryAccessCodeResponse::class, $response);
        $this->assertIsArray($response->getErrors());
        $this->assertEmpty($response->getErrors());
        $this->assertEquals($accessCode, $response->AccessCode);
    }
}
