<?php

declare(strict_types=1);

namespace Eway\Test\Integration;

use Eway\Rapid\Model\Response\QueryTransactionResponse;
use Eway\Rapid\Model\Transaction;

class InvoiceTest extends AbstractTestCase
{
    /** @var array $responseData */
    private array $responseData = [
        'AuthorisationCode' => '123456',
        'ResponseCode' => '00',
        'ResponseMessage' => 'A2000',
        'InvoiceNumber' => 'invoice_number_1234567890',
        'InvoiceReference' => '',
        'TotalAmount' => 1000,
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
            'TokenCustomerID' => 'ID',
            'Reference' => 'reference',
            'Title' => 'Mr.',
        ],
    ];

    /**
     * @dataProvider methodDataProvider
     * @param string $method
     * @param string $param
     * @return void
     */
    public function testQueryInvoice(string $method, string $param): void
    {
        $this->setUpCurl(200, ['Transactions' => [$this->responseData], 'Errors' => '']);
        $response = $this->client->{$method}($param);
        $this->assertInstanceOf(QueryTransactionResponse::class, $response);
        $this->assertIsArray($response->getErrors());
        $this->assertEmpty($response->getErrors());
        $this->assertEquals([new Transaction($this->responseData)], $response->Transactions);
    }

    /**
     * @return array[]
     */
    public function methodDataProvider(): array
    {
        return [
            ['queryInvoiceNumber', 'invoice-number'],
            ['queryInvoiceReference', 'invoice-reference'],
        ];
    }
}
