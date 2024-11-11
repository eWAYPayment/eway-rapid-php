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
        $this->assertEquals('8f35c4b6-aca1-4baf-873b-186ca013d3d6', $response->SettlementSummaries[0]['SettlementID']);
    }
}
