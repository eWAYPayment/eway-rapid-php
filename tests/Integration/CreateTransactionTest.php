<?php

namespace Eway\Test\Integration;

use Eway\Rapid\Enum\ApiMethod;
use Eway\Rapid\Enum\ShippingMethod;
use Eway\Rapid\Enum\TransactionType;
use Eway\Rapid\Model\Response\CreateTransactionResponse;
use Eway\Test\AbstractClientTest;

/**
 * Class CreateTransactionTest.
 */
class CreateTransactionTest extends AbstractClientTest
{
    /**
     * Create Direct Connection Transaction with basic request
     */
    public function testCreateTransactionDirectConnectionWithBasicRequest()
    {
        $cardDetails = [
            'Name' => 'John Smith',
            'Number' => '4444333322221111',
            'ExpiryMonth' => '12',
            'ExpiryYear' => '25',
            'CVN' => '123',
        ];

        $customer = [
            'CardDetails' => $cardDetails,
        ];

        $payment = [
            'TotalAmount' => 1000,
        ];

        $transaction = [
            'Customer' => $customer,
            'Payment' => $payment,
            'Capture' => true,
            'TransactionType' => TransactionType::PURCHASE,
        ];

        $response = $this->client->createTransaction(ApiMethod::DIRECT, $transaction);
        $this->assertInstanceOf(CreateTransactionResponse::getClass(), $response);
        $this->assertTrue(is_array($response->getErrors()));
        $this->assertEmpty($response->getErrors());
        $this->assertTrue($response->TransactionStatus);
        $this->assertCustomer($customer, $response);
        $this->assertPayment($payment, $response);
    }

    /**
     * Create Direct Connection Transaction with full request
     */
    public function testCreateTransactionDirectConnectionWithFullRequest()
    {
        $cardDetails = [
            "Name" => "John Smith",
            "Number" => "4444333322221111",
            "ExpiryMonth" => "12",
            "ExpiryYear" => "25",
            "StartMonth" => "01",
            "StartYear" => "13",
            "IssueNumber" => "01",
            "CVN" => "123",
        ];
        $shippingAddress = [
            "ShippingMethod" => ShippingMethod::NEXT_DAY,
            "FirstName" => "John",
            "LastName" => "Smith",
            "Street1" => "Level 5",
            "Street2" => "369 Queen Street",
            "City" => "Sydney",
            "State" => "NSW",
            "Country" => "au",
            "PostalCode" => "2000",
            "Phone" => "09 889 0986",
        ];
        $items = [
            [
                "SKU" => "12345678901234567890",
                "Description" => "Item Description 1",
                "Quantity" => 1,
                "UnitCost" => 400,
                "Tax" => 100,
            ],
            [
                "SKU" => "123456789012",
                "Description" => "Item Description 2",
                "Quantity" => 1,
                "UnitCost" => 400,
                "Tax" => 100,
            ],
        ];
        $options = [
            [
                "Value" => "Option1",
            ],
            [
                "Value" => "Option2",
            ],
        ];
        $payment = [
            "TotalAmount" => 1000,
            "InvoiceNumber" => "Inv 21540",
            "InvoiceDescription" => "Individual Invoice Description",
            "InvoiceReference" => "513456",
            "CurrencyCode" => "AUD",
        ];
        $customer = [
            "Reference" => "A12345",
            "Title" => "Mr.",
            "FirstName" => "John",
            "LastName" => "Smith",
            "CompanyName" => "Demo Shop 123",
            "JobDescription" => "Developer",
            "Street1" => "Level 5",
            "Street2" => "369 Queen Street",
            "City" => "Sydney",
            "State" => "NSW",
            "PostalCode" => "2000",
            "Country" => "au",
            "Phone" => "09 889 0986",
            "Mobile" => "09 889 6542",
            "Email" => "demo@example.org",
            "Url" => "http://www.ewaypayments.com",
            "CardDetails" => $cardDetails,
        ];
        $transaction = [
            "Customer" => $customer,
            "ShippingAddress" => $shippingAddress,
            "Items" => $items,
            "Options" => $options,
            "Payment" => $payment,
            "DeviceID" => "D1234",
            "CustomerIP" => "127.0.0.1",
            "PartnerID" => "ID",
            "TransactionType" => TransactionType::PURCHASE,
            "Capture" => true,
        ];

        $response = $this->client->createTransaction(ApiMethod::DIRECT, $transaction);

        $this->assertInstanceOf(CreateTransactionResponse::getClass(), $response);
        $this->assertTrue(is_array($response->getErrors()));
        $this->assertEmpty($response->getErrors());
        $this->assertTrue($response->TransactionStatus);
        $this->assertCustomer($customer, $response);
        $this->assertPayment($payment, $response);
        $this->assertEquals($transaction['TransactionType'], $response->TransactionType);
    }

    /**
     * Create Responsive Shared Transaction with basic request
     */
    public function testCreateTransactionResponsiveSharedWithBasicRequest()
    {
        $payment = [
            "TotalAmount" => 100,
        ];

        $transaction = [
            "Payment" => $payment,
            "RedirectUrl" => "http://www.eway.com.au",
            "CancelUrl" => "http://www.eway.com.au",
            "TransactionType" => TransactionType::PURCHASE,
            'Capture' => true,
        ];

        $response = $this->client->createTransaction(ApiMethod::RESPONSIVE_SHARED, $transaction);

        $this->assertInstanceOf(CreateTransactionResponse::getClass(), $response);
        $this->assertTrue(is_array($response->getErrors()));
        $this->assertEmpty($response->getErrors());
        $this->assertPayment($payment, $response);
        $this->assertNotEmpty($response->SharedPaymentUrl);
        $this->assertNotEmpty($response->AccessCode);
    }

    /**
     * Create Responsive Shared Transaction with full request
     */
    public function testCreateTransactionResponsiveSharedWithFullRequest()
    {
        $customer = [
            'Reference' => 'A12345',
            'Title' => 'Mr.',
            'FirstName' => 'John',
            'LastName' => 'Smith',
            'CompanyName' => 'Demo Shop 123',
            'JobDescription' => 'Developer',
            'Street1' => 'Level 5',
            'Street2' => '369 Queen Street',
            'City' => 'Sydney',
            'State' => 'NSW',
            'PostalCode' => '2000',
            'Country' => 'au',
            'Phone' => '09 889 0986',
            'Mobile' => '09 889 6542',
            'Email' => 'demo@example.org',
            'Url' => 'http://www.ewaypayments.com',
        ];

        $shippingAddress = [
            "ShippingMethod" => ShippingMethod::NEXT_DAY,
            'FirstName' => 'John',
            'LastName' => 'Smith',
            'Street1' => 'Level 5',
            'Street2' => '369 Queen Street',
            'City' => 'Sydney',
            'State' => 'NSW',
            'Country' => 'au',
            'PostalCode' => '2000',
            'Phone' => '09 889 0986',
        ];
        $items = [
            [
                'SKU' => '12345678901234567890',
                'Description' => 'Item Description 1',
                'Quantity' => 1,
                'UnitCost' => 400,
                'Tax' => 100,
                'Total' => 500,
            ],
            [
                'SKU' => '123456789012',
                'Description' => 'Item Description 2',
                'Quantity' => 1,
                'UnitCost' => 400,
                'Tax' => 100,
                'Total' => 500,
            ],
        ];
        $options = [
            [
                'Value' => 'Option1',
            ],
            [
                'Value' => 'Option2',
            ],
        ];
        $payment = [
            'TotalAmount' => 1000,
            'InvoiceNumber' => 'Inv 21540',
            'InvoiceDescription' => 'Individual Invoice Description',
            'InvoiceReference' => '513456',
            'CurrencyCode' => 'AUD',
        ];
        $transaction = [
            'Customer' => $customer,
            'ShippingAddress' => $shippingAddress,
            'Items' => $items,
            'Options' => $options,
            'Payment' => $payment,
            'RedirectUrl' => 'http://www.eway.com.au',
            'CancelUrl' => 'http://www.eway.com.au',
            'DeviceID' => 'D1234',
            'CustomerIP' => '127.0.0.1',
            'PartnerID' => 'ID',
            'TransactionType' => TransactionType::PURCHASE,
            'LogoUrl' => 'https://mysite.com/images/logo4eway.jpg',
            'HeaderText' => 'My Site Header Text',
            'Language' => 'EN',
            'CustomerReadOnly' => true,
            'CustomView' => 'bootstrap',
            'VerifyCustomerPhone' => false,
            'VerifyCustomerEmail' => false,
            'Capture' => true,
        ];

        $response = $this->client->createTransaction(ApiMethod::RESPONSIVE_SHARED, $transaction);

        $this->assertInstanceOf(CreateTransactionResponse::getClass(), $response);
        $this->assertTrue(is_array($response->getErrors()));
        $this->assertEmpty($response->getErrors());
        $this->assertNotEmpty($response->SharedPaymentUrl);
        $this->assertNotEmpty($response->AccessCode);
        $this->assertNotEmpty($response->FormActionURL);
        $this->assertCustomer($customer, $response);
        $this->assertPayment($payment, $response);
    }

    /**
     * Create Transparent Redirect Transaction with basic request
     */
    public function testCreateTransactionTransparentRedirectWithBasicRequest()
    {
        $payment = [
            "TotalAmount" => 100,
        ];

        $transaction = [
            "Payment" => $payment,
            "RedirectUrl" => "http://www.eway.com.au",
            "TransactionType" => TransactionType::PURCHASE,
            'Capture' => true,
        ];

        $response = $this->client->createTransaction(ApiMethod::TRANSPARENT_REDIRECT, $transaction);

        $this->assertInstanceOf(CreateTransactionResponse::getClass(), $response);
        $this->assertTrue(is_array($response->getErrors()));
        $this->assertEmpty($response->getErrors());
        $this->assertNotEmpty($response->AccessCode);
        $this->assertNotEmpty($response->FormActionURL);
        $this->assertPayment($payment, $response);
    }

    /**
     * Create Transparent Redirect Transaction with full request
     */
    public function testCreateTransactionTransparentRedirectWithFullRequest()
    {
        $customer = [
            'Reference' => 'A12345',
            'Title' => 'Mr.',
            'FirstName' => 'John',
            'LastName' => 'Smith',
            'CompanyName' => 'Demo Shop 123',
            'JobDescription' => 'Developer',
            'Street1' => 'Level 5',
            'Street2' => '369 Queen Street',
            'City' => 'Sydney',
            'State' => 'NSW',
            'PostalCode' => '2000',
            'Country' => 'au',
            'Phone' => '09 889 0986',
            'Mobile' => '09 889 6542',
            'Email' => 'demo@example.org',
            'Url' => 'http://www.ewaypayments.com',
        ];
        $shippingAddress = [
            "ShippingMethod" => ShippingMethod::NEXT_DAY,
            'FirstName' => 'John',
            'LastName' => 'Smith',
            'Street1' => 'Level 5',
            'Street2' => '369 Queen Street',
            'City' => 'Sydney',
            'State' => 'NSW',
            'Country' => 'au',
            'PostalCode' => '2000',
            'Phone' => '09 889 0986',
        ];
        $items = [
            [
                'SKU' => '12345678901234567890',
                'Description' => 'Item Description 1',
                'Quantity' => 1,
                'UnitCost' => 400,
                'Tax' => 100,
                'Total' => 500,
            ],
            [
                'SKU' => '123456789012',
                'Description' => 'Item Description 2',
                'Quantity' => 1,
                'UnitCost' => 400,
                'Tax' => 100,
                'Total' => 500,
            ],
        ];
        $options = [
            [
                'Value' => 'Option1',
            ],
            [
                'Value' => 'Option2',
            ],
        ];
        $payment = [
            'TotalAmount' => 1000,
            'InvoiceNumber' => 'Inv 21540',
            'InvoiceDescription' => 'Individual Invoice Description',
            'InvoiceReference' => '513456',
            'CurrencyCode' => 'AUD',
        ];
        $transaction = [
            'Customer' => $customer,
            'ShippingAddress' => $shippingAddress,
            'Items' => $items,
            'Options' => $options,
            'Payment' => $payment,
            'RedirectUrl' => 'http://www.eway.com.au',
            'DeviceID' => 'D1234',
            'CustomerIP' => '127.0.0.1',
            'PartnerID' => 'ID',
            'TransactionType' => TransactionType::PURCHASE,
            'Capture' => true,
        ];
        $response = $this->client->createTransaction(ApiMethod::TRANSPARENT_REDIRECT, $transaction);
        $this->assertInstanceOf(CreateTransactionResponse::getClass(), $response);
        $this->assertTrue(is_array($response->getErrors()));
        $this->assertEmpty($response->getErrors());
        $this->assertNotEmpty($response->AccessCode);
        $this->assertNotEmpty($response->FormActionURL);
        $this->assertCustomer($customer, $response);
        $this->assertPayment($payment, $response);
    }

    /**
     * Create Direct Connection Transaction with Capture flag on
     */
    public function testCreateTransactionDirectConnectionCaptureOn()
    {
        $customer = [
            'CardDetails' => [
                'Name' => 'John Smith',
                'Number' => '4444333322221111',
                'ExpiryMonth' => '12',
                'ExpiryYear' => '25',
                'CVN' => '123',
            ],
        ];
        $payment = [
            'TotalAmount' => 1000,
        ];
        $transaction = [
            'Customer' => $customer,
            'Payment' => $payment,
            'Capture' => true,
            'TransactionType' => TransactionType::PURCHASE,
        ];
        $response = $this->client->createTransaction(ApiMethod::DIRECT, $transaction);
        $this->assertInstanceOf(CreateTransactionResponse::getClass(), $response);
        $this->assertTrue(is_array($response->getErrors()));
        $this->assertEmpty($response->getErrors());
        $this->assertTrue($response->TransactionStatus);
    }

    /**
     * Create Direct Connection Transaction with Capture flag off
     */
    public function testCreateTransactionDirectConnectionCaptureOff()
    {
        $customer = [
            'CardDetails' => [
                'Name' => 'John Smith',
                'Number' => '4444333322221111',
                'ExpiryMonth' => '12',
                'ExpiryYear' => '25',
                'CVN' => '123',
            ],
        ];
        $payment = [
            'TotalAmount' => 1000,
        ];
        $transaction = [
            'Customer' => $customer,
            'Payment' => $payment,
            'Capture' => false,
            'TransactionType' => TransactionType::PURCHASE,
        ];
        $response = $this->client->createTransaction(ApiMethod::DIRECT, $transaction);
        $this->assertInstanceOf(CreateTransactionResponse::getClass(), $response);
        $this->assertTrue(is_array($response->getErrors()));
        $this->assertEmpty($response->getErrors());
        $this->assertTrue($response->TransactionStatus);
    }

    /**
     * Create Responsive Shared Transaction with Capture flag on and Customer has token
     */
    public function testCreateTransactionResponsiveSharedCaptureOnCustomerHasToken()
    {
        $tokenCustomer = [
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

        $createCustomerResponse = $this->client->createCustomer(ApiMethod::DIRECT, $tokenCustomer);

        $customer = [
            'TokenCustomerID' => $createCustomerResponse->Customer->TokenCustomerID,
        ];

        $payment = [
            'TotalAmount' => 1000,
        ];
        $transaction = [
            'Customer' => $customer,
            'Payment' => $payment,
            'RedirectUrl' => 'http://www.eway.com.au',
            'CancelUrl' => 'http://www.eway.com.au',
            'TransactionType' => TransactionType::PURCHASE,
            'Capture' => true,
        ];

        $response = $this->client->createTransaction(ApiMethod::RESPONSIVE_SHARED, $transaction);

        $this->assertInstanceOf(CreateTransactionResponse::getClass(), $response);
        $this->assertTrue(is_array($response->getErrors()));
        $this->assertEmpty($response->getErrors());
        $this->assertCustomer($tokenCustomer, $response, true);
        $this->assertEquals($customer['TokenCustomerID'], $response->Customer->TokenCustomerID);
        $this->assertPayment($payment, $response);
    }

    /**
     * Create Responsive Shared Transaction with Capture flag on and Customer has no token
     */
    public function testCreateTransactionResponsiveSharedCaptureOnCustomerNoToken()
    {
        $customer = [
            'Title' => 'Mr.',
            'FirstName' => 'John',
            'LastName' => 'Smith',
            'Country' => 'au',
        ];

        $payment = [
            'TotalAmount' => 1000,
        ];
        $transaction = [
            'Customer' => $customer,
            'Payment' => $payment,
            'RedirectUrl' => 'http://www.eway.com.au',
            'CancelUrl' => 'http://www.eway.com.au',
            'TransactionType' => TransactionType::PURCHASE,
            'Capture' => true,
        ];

        $response = $this->client->createTransaction(ApiMethod::RESPONSIVE_SHARED, $transaction);

        $this->assertInstanceOf(CreateTransactionResponse::getClass(), $response);
        $this->assertTrue(is_array($response->getErrors()));
        $this->assertEmpty($response->getErrors());
        $this->assertNotEmpty($response->SharedPaymentUrl);
        $this->assertNotEmpty($response->AccessCode);
        $this->assertNotEmpty($response->FormActionURL);
        $this->assertCustomer($customer, $response);
        $this->assertPayment($payment, $response);
    }

    /**
     * Create Responsive Shared Transaction with Capture flag off
     */
    public function testCreateTransactionResponsiveSharedCaptureOff()
    {
        $payment = [
            "TotalAmount" => 100,
        ];

        $transaction = [
            "Payment" => $payment,
            "RedirectUrl" => "http://www.eway.com.au",
            "CancelUrl" => "http://www.eway.com.au",
            "TransactionType" => TransactionType::PURCHASE,
            'Capture' => false,
        ];

        $response = $this->client->createTransaction(ApiMethod::RESPONSIVE_SHARED, $transaction);

        $this->assertInstanceOf(CreateTransactionResponse::getClass(), $response);
        $this->assertTrue(is_array($response->getErrors()));
        $this->assertEmpty($response->getErrors());
        $this->assertPayment($payment, $response);
        $this->assertNotEmpty($response->SharedPaymentUrl);
        $this->assertNotEmpty($response->AccessCode);
    }

    /**
     * Create Transparent Redirect Transaction with Capture flag on and Customer has token
     */
    public function testCreateTransactionTransparentRedirectCaptureOnCustomerHasToken()
    {
        $tokenCustomer = [
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

        $createCustomerResponse = $this->client->createCustomer(ApiMethod::DIRECT, $tokenCustomer);

        $customer = [
            'TokenCustomerID' => $createCustomerResponse->Customer->TokenCustomerID,
        ];

        $payment = [
            "TotalAmount" => 100,
        ];

        $transaction = [
            'Customer' => $customer,
            "Payment" => $payment,
            "RedirectUrl" => "http://www.eway.com.au",
            "TransactionType" => TransactionType::PURCHASE,
            'Capture' => true,
        ];

        $response = $this->client->createTransaction(ApiMethod::TRANSPARENT_REDIRECT, $transaction);

        $this->assertInstanceOf(CreateTransactionResponse::getClass(), $response);
        $this->assertTrue(is_array($response->getErrors()));
        $this->assertEmpty($response->getErrors());
        $this->assertNotEmpty($response->AccessCode);
        $this->assertNotEmpty($response->FormActionURL);
        $this->assertCustomer($tokenCustomer, $response, true);
        $this->assertEquals($customer['TokenCustomerID'], $response->Customer->TokenCustomerID);
        $this->assertPayment($payment, $response);
    }

    /**
     * Create Transparent Redirect Transaction with Capture flag on and Customer has no token
     */
    public function testCreateTransactionTransparentRedirectCaptureOnCustomerNoToken()
    {
        $payment = [
            "TotalAmount" => 100,
        ];

        $transaction = [
            "Payment" => $payment,
            "RedirectUrl" => "http://www.eway.com.au",
            "TransactionType" => TransactionType::PURCHASE,
            'Capture' => true,
        ];

        $response = $this->client->createTransaction(ApiMethod::TRANSPARENT_REDIRECT, $transaction);

        $this->assertInstanceOf(CreateTransactionResponse::getClass(), $response);
        $this->assertTrue(is_array($response->getErrors()));
        $this->assertEmpty($response->getErrors());
        $this->assertNotEmpty($response->AccessCode);
        $this->assertNotEmpty($response->FormActionURL);
        $this->assertPayment($payment, $response);
    }

    /**
     * Create Transparent Redirect Transaction with Capture flag off
     */
    public function testCreateTransactionTransparentRedirectCaptureOff()
    {
        $payment = [
            "TotalAmount" => 100,
        ];

        $transaction = [
            "Payment" => $payment,
            "RedirectUrl" => "http://www.eway.com.au",
            "TransactionType" => TransactionType::PURCHASE,
            'Capture' => false,
        ];

        $response = $this->client->createTransaction(ApiMethod::TRANSPARENT_REDIRECT, $transaction);

        $this->assertInstanceOf(CreateTransactionResponse::getClass(), $response);
        $this->assertTrue(is_array($response->getErrors()));
        $this->assertEmpty($response->getErrors());
        $this->assertNotEmpty($response->AccessCode);
        $this->assertNotEmpty($response->FormActionURL);
        $this->assertPayment($payment, $response);
    }

    /**
     * Create Authorisation Transaction
     */
    public function testCreateTransactionAuthorisation()
    {
        $customer = [
            'CardDetails' => $cardDetails = [
                'Name' => 'John Smith',
                'Number' => '4444333322221111',
                'ExpiryMonth' => '12',
                'ExpiryYear' => '25',
                'CVN' => '123',
            ],
        ];

        $payment = [
            'TotalAmount' => 1000,
        ];

        $authTransaction = [
            'Customer' => $customer,
            'Payment' => $payment,
            'Capture' => false,
            'TransactionType' => TransactionType::PURCHASE,
        ];

        $authTransactionResponse = $this->client->createTransaction(ApiMethod::DIRECT, $authTransaction);

        $transaction = [
            'Payment' => $payment,
            'TransactionID' => $authTransactionResponse->TransactionID,
        ];

        $response = $this->client->createTransaction(ApiMethod::AUTHORISATION, $transaction);
        $this->assertInstanceOf(CreateTransactionResponse::getClass(), $response);
        $this->assertTrue(is_array($response->getErrors()));
        $this->assertEmpty($response->getErrors());
        $this->assertTrue($response->TransactionStatus);
    }

    /**
     * Create Wallet Transaction with Capture flag on
     *
     * @todo there's no way to generate a valid Wallet ID to test this case
     */
    public function createTransactionWalletCaptureOn()
    {
        $payment = [
            'TotalAmount' => 1000,
        ];
        $transaction = [
            'Payment' => $payment,
            'Capture' => true,
            'TransactionType' => TransactionType::PURCHASE,
            'SecuredCardData' => 'VCOCallID:123456',
        ];
        $response = $this->client->createTransaction(ApiMethod::WALLET, $transaction);
        $this->assertInstanceOf(CreateTransactionResponse::getClass(), $response);
        $this->assertTrue(is_array($response->getErrors()));
        $this->assertEmpty($response->getErrors());
        $this->assertTrue($response->TransactionStatus);
    }

    /**
     * Create Wallet Transaction with Capture flag off
     *
     * @todo there's no way to generate a valid Wallet ID to test this case
     */
    public function createTransactionWalletCaptureOff()
    {
        $payment = [
            'TotalAmount' => 1000,
        ];
        $transaction = [
            'Payment' => $payment,
            'Capture' => false,
            'TransactionType' => TransactionType::PURCHASE,
            'SecuredCardData' => 'VCOCallID:123456',
        ];
        $response = $this->client->createTransaction(ApiMethod::WALLET, $transaction);
        $this->assertInstanceOf(CreateTransactionResponse::getClass(), $response);
        $this->assertTrue(is_array($response->getErrors()));
        $this->assertEmpty($response->getErrors());
        $this->assertTrue($response->TransactionStatus);
    }
}
