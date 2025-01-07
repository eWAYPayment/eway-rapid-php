<?php

declare(strict_types=1);

namespace Eway\Test\Integration;

use Eway\Rapid\Model\Response\SettlementSearchResponse;

class SettlementTest extends AbstractTestCase
{
    private array $responseData = [
        'SettlementSummaries' => [
            [
                'SettlementID' => '53e78b14-ac2c-4b1b-a099-a12c6d5f30bc',
                'Currency' => '36',
                'CurrencyCode' => 'AUD',
                'TotalCredit' => 97100,
                'TotalDebit' => 320,
                'TotalFees' => 100,
                'TotalBalance' => 96680,
            ]
        ],
        'SettlementTransactions' => [
            [
                'SettlementID' => '53e78b14-ac2c-4b1b-a099-a12c6d5f30bc',
                'eWAYCustomerID' => 87654321,
                'Currency' => '36',
                'CurrencyCode' => 'AUD',
                'TransactionID' => 11259196,
                'TxnReference' => '0000000011259196',
                'CardType' => 'VI',
                'FeePerTransaction' => 50,
                'Amount' => 1000,
                'TransactionType' => '1',
                'TransactionDateTime' => '/Date(1422795600000)/',
                'SettlementDateTime' => '/Date(1422795600000)/',
            ]
        ],
        'Errors' => '',
    ];

    /**
     * @return void
     */
    public function testSettlementSearch(): void
    {
        $this->setUpCurl(200, $this->responseData);
        $response = $this->client->settlementSearch(['Page' => 1, 'PageSize' => 10]);
        $this->assertInstanceOf(SettlementSearchResponse::class, $response);
        $this->assertIsArray($response->getErrors());
        $this->assertEmpty($response->getErrors());
        $this->assertEquals($this->responseData['SettlementSummaries'], $response->SettlementSummaries);
        $this->assertEquals($this->responseData['SettlementTransactions'], $response->SettlementTransactions);
    }
}
