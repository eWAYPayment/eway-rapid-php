<?php

namespace Eway\Test\Unit\Client;

use Eway\Rapid\Contract\Client;
use Eway\Rapid\Contract\HttpService;
use Eway\Rapid\Model\Response\SettlementSearchResponse;
use Eway\Test\AbstractClientTest;
use Prophecy\Argument;
use Prophecy\Prophecy\MethodProphecy;

/**
 * Class SettlementSearchTest.
 */
class SettlementSearchTest extends AbstractClientTest
{
    /**
     * Test Settlement Search
     */
    public function testSettlementSearchShouldCallHttpServiceGetSettlementSearch()
    {
        $search = [
            'ReportMode' => 'Both',
            'SettlementDate' => '2015-02-02',
        ];

        $mockResponse = $this->getResponse([
            'SettlementSummaries' => [
                [
                    'SettlementID' => '53e78b14-ac2c-4b1b-a099-a12c6d5f30bc',
                    'Currency' => 36,
                    'CurrencyCode' => 'AUD',
                    'TotalCredit' => 97100,
                    'TotalDebit' => 320,
                    'TotalBalance' => 96780,
                    'BalancePerCardType' => [
                        [
                            'CardType' => 'VI',
                            'NumberOfTransactions' => 14,
                            'Credit' => 97100,
                            'Debit' => 320,
                            'Balance' => 96780,
                        ],
                    ],
                ],
            ],
            'SettlementTransactions' => [
                [
                    'SettlementID' => '53e78b14-ac2c-4b1b-a099-a12c6d5f30bc',
                    'CurrencyCardTypeTransactionID' => '36:VI:11258912',
                    'eWAYCustomerID' => '91312168',
                    'Currency' => 36,
                    'CurrencyCode' => 'AUD',
                    'TransactionID' => '11258912',
                    'TxnReference' => '0000000011258912',
                    'CardType' => 'VI',
                    'Amount' => 100,
                    'TransactionType' => '1',
                    'TransactionDateTime' => '/Date(1422795600000)/',
                    'SettlementDateTime' => '/Date(1422795600000)/',
                ],
                [
                    'SettlementID' => '53e78b14-ac2c-4b1b-a099-a12c6d5f30bc',
                    'CurrencyCardTypeTransactionID' => '36:VI:11259196',
                    'eWAYCustomerID' => '91312168',
                    'Currency' => 36,
                    'CurrencyCode' => 'AUD',
                    'TransactionID' => '11259196',
                    'TxnReference' => '0000000011259196',
                    'CardType' => 'VI',
                    'Amount' => 1000,
                    'TransactionType' => '1',
                    'TransactionDateTime' => '/Date(1422795600000)/',
                    'SettlementDateTime' => '/Date(1422795600000)/',
                ],
                [
                    'SettlementID' => '53e78b14-ac2c-4b1b-a099-a12c6d5f30bc',
                    'CurrencyCardTypeTransactionID' => '36:VI:11259550',
                    'eWAYCustomerID' => '91312168',
                    'Currency' => 36,
                    'CurrencyCode' => 'AUD',
                    'TransactionID' => '11259550',
                    'TxnReference' => '0000000011259550',
                    'CardType' => 'VI',
                    'Amount' => 1000,
                    'TransactionType' => '1',
                    'TransactionDateTime' => '/Date(1422795600000)/',
                    'SettlementDateTime' => '/Date(1422795600000)/',
                ],
            ],
            'Errors' => '',
        ]);

        $httpService = $this->prophet->prophesize('Eway\Rapid\Contract\HttpService');

        /** @var MethodProphecy $getSettlementSearchStub */
        $getSettlementSearchStub = $httpService->getSettlementSearch(Argument::type('array'));
        $getSettlementSearchStub->withArguments([$search])->willReturn($mockResponse)->shouldBeCalled();

        /** @var HttpService $stub */
        $stub = $httpService->reveal();
        $this->client->setHttpService($stub);


        $response = $this->client->settlementSearch($search);

        $this->assertInstanceOf(SettlementSearchResponse::getClass(), $response);
        $this->assertTrue(is_array($response->getErrors()));
        $this->assertEmpty($response->getErrors());
        $this->assertEquals('53e78b14-ac2c-4b1b-a099-a12c6d5f30bc', $response->SettlementSummaries[0]['SettlementID']);
    }

    public function testSettlementSearchReturnInvalidData()
    {
        $search = [
            'ReportMode' => 'Both',
            'SettlementDate' => '2015-01-02',
        ];


        $mockResponse = $this->getResponse([
            'SettlementSummaries' => null,
            'SettlementTransactions' => null,
            'Errors' => 'If you are querying the settlement report with this date range for the first time, the data will be available in 60 mins approx. Thank you.',
        ]);

        $httpService = $this->prophet->prophesize('Eway\Rapid\Contract\HttpService');

        /** @var MethodProphecy $getSettlementSearchStub */
        $getSettlementSearchStub = $httpService->getSettlementSearch(Argument::type('array'));
        $getSettlementSearchStub->withArguments([$search])->willReturn($mockResponse)->shouldBeCalled();

        /** @var HttpService $stub */
        $stub = $httpService->reveal();
        $this->client->setHttpService($stub);


        $response = $this->client->settlementSearch($search);

        $this->assertInstanceOf(SettlementSearchResponse::getClass(), $response);
        $this->assertTrue(is_array($response->getErrors()));
        $this->assertNotEmpty($response->getErrors());
    }
}
