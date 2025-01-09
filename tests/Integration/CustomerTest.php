<?php

declare(strict_types=1);

namespace Eway\Test\Integration;

use Eway\Rapid\Enum\ApiMethod;
use Eway\Rapid\Exception\MethodNotImplementedException;
use Eway\Rapid\Model\Customer;
use Eway\Rapid\Model\Response\CreateCustomerResponse;
use Eway\Rapid\Model\Response\QueryCustomerResponse;

class CustomerTest extends AbstractTestCase
{
    /** @var array $customer */
    private $customer = [
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

    /**
     * @dataProvider createCustomerDataProvider
     * @param string $apiMethod
     * @return void
     */
    public function testCreateCustomer(string $apiMethod): void
    {
        $customer = $this->customer;
        $customer['PaymentInstrument'] = 'payment-instrument';

        $this->setUpCurl(200, ['Customer' => $customer]);
        $response = $this->client->createCustomer($apiMethod, $customer);

        $this->assertInstanceOf(CreateCustomerResponse::class, $response);
        $this->assertIsArray($response->getErrors());
        $this->assertEmpty($response->getErrors());
        $this->assertEquals($this->customer, $response->Customer->toArray());
    }

    /**
     * @return array[]
     */
    public function createCustomerDataProvider(): array
    {
        return [
            [ApiMethod::DIRECT],
            [ApiMethod::WALLET],
            [ApiMethod::RESPONSIVE_SHARED],
            [ApiMethod::TRANSPARENT_REDIRECT],
        ];
    }

    /**
     * @dataProvider updateCustomerDataProvider
     * @param string $apiMethod
     * @return void
     */
    public function testUpdateCustomer(string $apiMethod): void
    {
        $this->setUpCurl(200, ['Customer' => $this->customer]);
        $response = $this->client->updateCustomer($apiMethod, $this->customer);

        $this->assertInstanceOf(CreateCustomerResponse::class, $response);
        $this->assertIsArray($response->getErrors());
        $this->assertEmpty($response->getErrors());
        $this->assertEquals($this->customer, $response->Customer->toArray());
    }

    /**
     * @return array[]
     */
    public function updateCustomerDataProvider(): array
    {
        return [
            [ApiMethod::DIRECT],
            [ApiMethod::RESPONSIVE_SHARED],
            [ApiMethod::TRANSPARENT_REDIRECT],
        ];
    }

    /**
     * @return void
     */
    public function testCreateCustomerError(): void
    {
        $this->expectException(MethodNotImplementedException::class);
        $this->client->createCustomer(ApiMethod::AUTHORISATION, $this->customer);
    }

    /**
     * @return void
     */
    public function testUpdateCustomerError(): void
    {
        $this->expectException(MethodNotImplementedException::class);
        $this->client->updateCustomer(ApiMethod::AUTHORISATION, $this->customer);
    }

    /**
     * @return void
     */
    public function testQueryCustomer(): void
    {
        $token = 'token-customer-id';
        $customer = $this->customer;
        $customer['TokenCustomerID'] = $token;

        $this->setUpCurl(200, ['Customers' => [$customer]]);
        $response = $this->client->queryCustomer($token);

        $this->assertInstanceOf(QueryCustomerResponse::class, $response);
        $this->assertIsArray($response->getErrors());
        $this->assertEmpty($response->getErrors());
        $this->assertEquals([new Customer($customer)], $response->Customers);
    }
}
