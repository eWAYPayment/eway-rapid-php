<?php

namespace Eway\Test\Integration;

use Eway\Rapid\Enum\ApiMethod;
use Eway\Rapid\Model\Response\CreateCustomerResponse;
use Eway\Test\AbstractClientTest;

/**
 * Class UpdateCustomerTest.
 */
class UpdateCustomerTest extends AbstractClientTest
{
    
    private $tokenCustomerId;
    
    protected function setup()
    {
        parent::setUp();
        
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

        $this->tokenCustomerId = $response->Customer->TokenCustomerID;
    }
    
    /**
     * Update Customer Token for Direct Connection Transaction
     */
    public function testUpdateCustomerDirectConnection()
    {
        $newCustomer = [
            'TokenCustomerID' => $this->tokenCustomerId,
            'Title' => 'Mrs.',
            'FirstName' => 'Jane',
            'LastName' => 'Doe',
            'Country' => 'vn',
            'CardDetails' => [
                'Name' => 'Jane Doe',
                'Number' => '4444333322221111',
                'ExpiryMonth' => '12',
                'ExpiryYear' => '24',
                'CVN' => '321',
            ],
        ];

        $newResponse = $this->client->updateCustomer(ApiMethod::DIRECT, $newCustomer);

        $this->assertInstanceOf(CreateCustomerResponse::getClass(), $newResponse);
        $this->assertTrue(is_array($newResponse->getErrors()));
        $this->assertEmpty($newResponse->getErrors());
        $this->assertCustomer($newCustomer, $newResponse);
    }

    /**
     * Update Customer Token for Responsive Shared Transaction
     */
    public function testUpdateCustomerResponsiveShared()
    {
        /*
         * Update Token
         */
        $newCustomer = [
            'TokenCustomerID' => $this->tokenCustomerId,
            'Title' => 'Mrs.',
            'FirstName' => 'Jane',
            'LastName' => 'Doe',
            'Country' => 'vn',
            'RedirectUrl' => 'http://www.eway.com.au',
            'CancelUrl' => "http://www.eway.com.au",
        ];

        $updateCustomerResponse = $this->client->updateCustomer(ApiMethod::RESPONSIVE_SHARED, $newCustomer);
        
        /*
         * Assert
         */
        $this->assertInstanceOf(CreateCustomerResponse::getClass(), $updateCustomerResponse);
        $this->assertTrue(is_array($updateCustomerResponse->getErrors()));
        $this->assertEmpty($updateCustomerResponse->getErrors());
        $this->assertCustomer($newCustomer, $updateCustomerResponse);
    }

    /**
     * Update Customer Token for Transparent Redirect Transaction
     */
    public function testUpdateCustomerTransparentRedirect()
    {
        $newCustomer = [
            'TokenCustomerID' => $this->tokenCustomerId,
            'Title' => 'Mrs.',
            'FirstName' => 'Jane',
            'LastName' => 'Doe',
            'Country' => 'vn',
            'RedirectUrl' => 'http://www.eway.com.au',
        ];

        $updateCustomerResponse = $this->client->updateCustomer(ApiMethod::TRANSPARENT_REDIRECT, $newCustomer);

        /*
         * Assert
         */
        $this->assertInstanceOf(CreateCustomerResponse::getClass(), $updateCustomerResponse);
        $this->assertTrue(is_array($updateCustomerResponse->getErrors()));
        $this->assertEmpty($updateCustomerResponse->getErrors());
        $this->assertCustomer($newCustomer, $updateCustomerResponse);
    }
}
