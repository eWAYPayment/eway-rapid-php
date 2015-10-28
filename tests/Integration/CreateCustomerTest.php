<?php

namespace Eway\Test\Integration;

use Eway\Rapid\Enum\ApiMethod;
use Eway\Rapid\Model\Response\CreateCustomerResponse;
use Eway\Test\AbstractClientTest;

/**
 * Class CreateCustomerTest.
 */
class CreateCustomerTest extends AbstractClientTest
{
    /**
     * Create Customer Token for Direct Connection Transaction
     */
    public function testCreateCustomerDirectConnection()
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

        $response = $this->client->createCustomer(ApiMethod::DIRECT, $customer);
        $this->assertInstanceOf(CreateCustomerResponse::getClass(), $response);
        $this->assertTrue(is_array($response->getErrors()));
        $this->assertEmpty($response->getErrors());
        $this->assertCustomer($customer, $response);
        $this->assertNotEmpty($response->Customer->TokenCustomerID);
    }

    /**
     * Create Customer Token for Responsive Shared Transaction
     */
    public function testCreateCustomerResponsiveShared()
    {
        $customer = [
            'Title' => 'Mr.',
            'FirstName' => 'John',
            'LastName' => 'Smith',
            'Country' => 'au',
            'RedirectUrl' => 'http://www.eway.com.au',
            'CancelUrl' => 'http://www.eway.com.au',
        ];

        $response = $this->client->createCustomer(ApiMethod::RESPONSIVE_SHARED, $customer);
        $this->assertInstanceOf(CreateCustomerResponse::getClass(), $response);
        $this->assertTrue(is_array($response->getErrors()));
        $this->assertEmpty($response->getErrors());
    }

    /**
     * Create Customer Token for Transparent Redirect Transaction
     */
    public function testCreateCustomerTransparentRedirect()
    {
        $customer = [
            'Title' => 'Mr.',
            'FirstName' => 'John',
            'LastName' => 'Smith',
            'Country' => 'au',
            'RedirectUrl' => 'http://www.eway.com.au',
        ];

        $response = $this->client->createCustomer(ApiMethod::TRANSPARENT_REDIRECT, $customer);
        $this->assertInstanceOf(CreateCustomerResponse::getClass(), $response);
        $this->assertTrue(is_array($response->getErrors()));
        $this->assertEmpty($response->getErrors());
    }
}
