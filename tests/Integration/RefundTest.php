<?php

declare(strict_types=1);

namespace Eway\Test\Integration;

use Eway\Rapid\Model\Refund;
use Eway\Rapid\Model\Response\RefundResponse;

class RefundTest extends AbstractTestCase
{
    /** @var array $responseData */
    private array $responseData = [
        'TransactionID' => '12345',
        'TotalAmount' => 100,
        'InvoiceNumber' => 'invoice-12345',
        'InvoiceReference' => '12345',
        'CurrencyCode' => 'AUD',
    ];

    /**
     * @dataProvider methodDataProvider
     * @param string $method
     * @param $param
     * @return void
     */
    public function testRefund(string $method, $param): void
    {
        $this->setUpCurl(200, ['Refund' => $this->responseData]);
        $response = $this->client->{$method}($param);

        $this->assertInstanceOf(RefundResponse::class, $response);
        $this->assertIsArray($response->getErrors());
        $this->assertEmpty($response->getErrors());
        $this->assertEquals($this->responseData, $response->Refund->toArray());
        $this->assertEquals(json_encode($this->responseData), $response->Refund->__toString());
    }

    /**
     * @return array
     */
    public function methodDataProvider(): array
    {
        return [
            ['refund', new Refund(['Refund' => ['TransactionID' => '12345']])],
            ['cancelTransaction', '12345'],
        ];
    }
}
