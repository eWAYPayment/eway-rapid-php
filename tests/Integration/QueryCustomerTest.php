<?php

namespace Eway\Test\Integration;

use Eway\Rapid\Model\Response\QueryCustomerResponse;
use Eway\Rapid\Enum\ApiMethod;
use Eway\Test\AbstractClientTest;

/**
 * Class QueryCustomerTest.
 */
class QueryCustomerTest extends AbstractClientTest
{
    /**
     * Test Query Customer
     */
    public function testQueryCustomer()
    {
        $customer = [
            'Title' => 'Mr.',
            'FirstName' => 'John',
            'LastName' => 'Smith',
            'Country' => 'au',
            'CardDetails' => [
                'Name' => 'John Smith',
                'Number' => '4444333322221111',
                'ExpiryMonth' => '12',
                'ExpiryYear' => '25',
                'CVN' => '123',
            ],
        ];

        $createCustomerResponse = $this->client->createCustomer(ApiMethod::DIRECT, $customer);
        $customerTokenId = $createCustomerResponse->Customer->TokenCustomerID;

        $response = $this->client->queryCustomer($customerTokenId);
        $this->assertInstanceOf(QueryCustomerResponse::getClass(), $response);
        $this->assertTrue(is_array($response->getErrors()));
        $this->assertEmpty($response->getErrors());
        $this->assertEquals($customerTokenId, $response->Customers[0]->TokenCustomerID);
    }
}
