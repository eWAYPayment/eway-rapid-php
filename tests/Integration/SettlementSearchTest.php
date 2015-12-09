<?php

namespace Eway\Test\Integration;

use Eway\Rapid\Model\Response\SettlementSearchResponse;
use Eway\Test\AbstractClientTest;

/**
 * Class SettlementSearchTest.
 */
class SettlementSearchTest extends AbstractClientTest
{
    /**
     * Test Settlement Search
     */
    public function testSettlementSearch()
    {
        $search = [
            'ReportMode' => 'Both',
            'SettlementDate' => '2015-02-02',
        ];

        $response = $this->client->settlementSearch($search);
        $this->assertInstanceOf(SettlementSearchResponse::getClass(), $response);
        $this->assertTrue(is_array($response->getErrors()));
        $this->assertEmpty($response->getErrors());
        $this->assertEquals('53e78b14-ac2c-4b1b-a099-a12c6d5f30bc', $response->SettlementSummaries[0]['SettlementID']);
    }
}
