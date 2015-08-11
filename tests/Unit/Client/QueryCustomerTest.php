<?php

namespace Eway\Test\Unit\Client;

use Eway\Rapid\Contract\Client;
use Eway\Rapid\Contract\HttpService;
use Eway\Rapid\Model\Response\QueryCustomerResponse;
use Eway\Test\AbstractClientTest;
use Prophecy\Argument;
use Prophecy\Prophecy\MethodProphecy;

/**
 * Class QueryCustomerTest.
 */
class QueryCustomerTest extends AbstractClientTest
{
    /**
     * Test Query Customer
     */
    public function testQueryCustomerShouldCallHttpServiceGetCustomer()
    {
        $customerTokenId = 987654321098;


        $mockResponse = $this->getResponse([
            'Customers' => [
                [
                    'CardDetails' => [
                        'Number' => '444433XXXXXX1111',
                        'Name' => 'John Smith4',
                        'ExpiryMonth' => '12',
                        'ExpiryYear' => '25',
                        'StartMonth' => '',
                        'StartYear' => '',
                        'IssueNumber' => '',
                    ],
                    'TokenCustomerID' => $customerTokenId,
                    'Reference' => '',
                    'Title' => 'Mr.',
                    'FirstName' => 'John4',
                    'LastName' => 'Smith4',
                    'CompanyName' => '',
                    'JobDescription' => '',
                    'Street1' => '',
                    'Street2' => null,
                    'City' => '',
                    'State' => '',
                    'PostalCode' => '',
                    'Country' => 'au',
                    'Email' => '',
                    'Phone' => '',
                    'Mobile' => '',
                    'Comments' => '',
                    'Fax' => '',
                    'Url' => '',
                ],
            ],
            'Errors' => '',
        ]);

        $httpService = $this->prophet->prophesize('Eway\Rapid\Contract\HttpService');

        /** @var MethodProphecy $getCustomerStub */
        $getCustomerStub = $httpService->getCustomer(Argument::exact($customerTokenId));
        $getCustomerStub->withArguments([$customerTokenId])->willReturn($mockResponse)->shouldBeCalled();

        /** @var HttpService $stub */
        $stub = $httpService->reveal();
        $this->client->setHttpService($stub);


        $response = $this->client->queryCustomer($customerTokenId);

        $this->assertInstanceOf(QueryCustomerResponse::getClass(), $response);
        $this->assertTrue(is_array($response->getErrors()));
        $this->assertEmpty($response->getErrors());
        $this->assertEquals($customerTokenId, $response->Customers[0]->TokenCustomerID);
    }

    public function testQueryCustomerReturnInvalidData()
    {
        $customerTokenId = 987654321098;


        $mockResponse = $this->getResponse([
            'Customers' => false,
            'Errors' => '',
        ]);

        $httpService = $this->prophet->prophesize('Eway\Rapid\Contract\HttpService');

        /** @var MethodProphecy $getCustomerStub */
        $getCustomerStub = $httpService->getCustomer(Argument::exact($customerTokenId));
        $getCustomerStub->withArguments([$customerTokenId])->willReturn($mockResponse)->shouldBeCalled();

        /** @var HttpService $stub */
        $stub = $httpService->reveal();
        $this->client->setHttpService($stub);


        $response = $this->client->queryCustomer($customerTokenId);

        $this->assertInstanceOf(QueryCustomerResponse::getClass(), $response);
        $this->assertTrue(is_array($response->getErrors()));
        $this->assertNotEmpty($response->getErrors());
        $this->assertContains(Client::ERROR_INVALID_ARGUMENT, $response->getErrors());
    }
}
