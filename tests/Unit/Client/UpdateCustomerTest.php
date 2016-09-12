<?php

namespace Eway\Test\Unit\Client;

use Eway\Rapid\Contract\HttpService;
use Eway\Rapid\Enum\ApiMethod;
use Eway\Rapid\Enum\PaymentMethod;
use Eway\Rapid\Enum\TransactionType;
use Eway\Rapid\Model\Response\CreateCustomerResponse;
use Eway\Test\AbstractClientTest;
use Prophecy\Argument;
use Prophecy\Prophecy\MethodProphecy;

/**
 * Class UpdateCustomerTest.
 */
class UpdateCustomerTest extends AbstractClientTest
{
    /**
     * Update Customer Token for Direct Connection Transaction
     */
    public function testUpdateCustomerDirectConnectionShouldCallHttpServicePostTransaction()
    {
        $cardDetail = [
            'Name' => 'John Smith',
            'Number' => '4444333322221111',
            'ExpiryMonth' => '12',
            'ExpiryYear' => '25',
            'CVN' => '123',
        ];

        $customer = [
            'TokenCustomerID' => 987654321098,
            'Title' => 'Mr.',
            'FirstName' => 'John',
            'LastName' => 'Smith',
            'Country' => 'au',
            'CardDetails' => $cardDetail,
        ];

        $payment = ['TotalAmount' => 0];
        $transaction = [
            'Customer' => $customer,
            'Payment' => $payment,
            'Method' => PaymentMethod::UPDATE_TOKEN_CUSTOMER,
            'TransactionType' => TransactionType::MOTO,
        ];

        $mockTransaction = $transaction;
        $mockTransaction['Capture'] = true;

        $mockResponse = $this->getResponse([
            'AuthorisationCode' => null,
            'ResponseCode' => '00',
            'ResponseMessage' => 'A2000',
            'TransactionID' => null,
            'TransactionStatus' => false,
            'TransactionType' => 'MOTO',
            'BeagleScore' => null,
            'Verification' => [
                'CVN' => 0,
                'Address' => 0,
                'Email' => 0,
                'Mobile' => 0,
                'Phone' => 0,
            ],
            'Customer' => [
                'CardDetails' => [
                    'Number' => $cardDetail['Number'],
                    'Name' => $cardDetail['Name'],
                    'ExpiryMonth' => $cardDetail['ExpiryMonth'],
                    'ExpiryYear' => $cardDetail['ExpiryYear'],
                    'StartMonth' => null,
                    'StartYear' => null,
                    'IssueNumber' => null,
                ],
                'TokenCustomerID' => $customer['TokenCustomerID'],
                'Reference' => '',
                'Title' => $customer['Title'],
                'FirstName' => $customer['FirstName'],
                'LastName' => $customer['LastName'],
                'CompanyName' => '',
                'JobDescription' => '',
                'Street1' => '',
                'Street2' => '',
                'City' => '',
                'State' => '',
                'PostalCode' => '',
                'Country' => $customer['Country'],
                'Email' => '',
                'Phone' => '',
                'Mobile' => '',
                'Comments' => '',
                'Fax' => '',
                'Url' => '',
            ],
            'Payment' => [
                'TotalAmount' => $payment['TotalAmount'],
                'InvoiceNumber' => '',
                'InvoiceDescription' => '',
                'InvoiceReference' => '',
                'CurrencyCode' => 'AUD',
            ],
            'Errors' => '',
        ]);

        $httpService = $this->prophet->prophesize('Eway\Rapid\Contract\HttpService');

        /** @var MethodProphecy $postTransactionStub */
        $postTransactionStub = $httpService->postTransaction(Argument::type('array'));
        $postTransactionStub->withArguments([$mockTransaction])->willReturn($mockResponse)->shouldBeCalled();

        /** @var HttpService $stub */
        $stub = $httpService->reveal();
        $this->client->setHttpService($stub);


        $response = $this->client->updateCustomer(ApiMethod::DIRECT, $customer);

        $this->assertInstanceOf(CreateCustomerResponse::getClass(), $response);
        $this->assertTrue(is_array($response->getErrors()));
        $this->assertEmpty($response->getErrors());
        $this->assertCustomer($customer, $response);
        $this->assertNotEmpty($response->Customer->TokenCustomerID);
    }

    /**
     * Update Customer Token for Responsive Shared Transaction
     */
    public function testUpdateCustomerResponsiveSharedShouldCallHttpServicePostAccessCodeShared()
    {
        $customer = [
            'TokenCustomerID' => 987654321098,
            'RedirectUrl' => 'http://www.eway.com.au',
            'CustomView' => 'Bootstrap',
            'HeaderText' => 'Awesome Store',
        ];

        $payment = [
            'TotalAmount' => 0,
        ];

        $transaction = [
            'Customer' => $customer,
            'Payment' => $payment,
            'Method' => PaymentMethod::UPDATE_TOKEN_CUSTOMER,
            'TransactionType' => TransactionType::MOTO,
            'RedirectUrl' => $customer['RedirectUrl'],
            'CustomView' => $customer['CustomView'],
            'HeaderText' => $customer['HeaderText'],
        ];

        $mockTransaction = $transaction;
        $mockTransaction['Capture'] = true;

        $accessCode = 'F9802-v03wAxeAdj-Er4BcqJcip-Dqz1mk3ajbPkdGqpQxe9BAL01Kk4DX5iEcWLeztKwCEPWuPIY9jlDC6juJNTAdsWy4iYcT-qE3L14KCANlDN0GItsYzmA47G-c_rabblJ';
        $mockResponse = $this->getResponse([
            "SharedPaymentUrl" => "https://secure-au.sandbox.ewaypayments.com/sharedpage/sharedpayment?AccessCode=$accessCode",
            "AccessCode" => $accessCode,
            "Customer" => [
                "CardNumber" => "444433XXXXXX1111",
                "CardStartMonth" => "",
                "CardStartYear" => "",
                "CardIssueNumber" => "",
                "CardName" => "John Doe",
                "CardExpiryMonth" => "12",
                "CardExpiryYear" => "21",
                "IsActive" => true,
                "TokenCustomerID" => $customer['TokenCustomerID'],
                "Reference" => "",
                "Title" => "Mr.",
                "FirstName" => "John",
                "LastName" => "Doe",
                "CompanyName" => "",
                "JobDescription" => "",
                "Street1" => "",
                "Street2" => "",
                "City" => "",
                "State" => "",
                "PostalCode" => "",
                "Country" => "au",
                "Email" => "",
                "Phone" => "",
                "Mobile" => "",
                "Comments" => "",
                "Fax" => "",
                "Url" => "",
            ],
            "Payment" => [
                "TotalAmount" => $payment['TotalAmount'],
                "InvoiceNumber" => null,
                "InvoiceDescription" => null,
                "InvoiceReference" => null,
                "CurrencyCode" => "AUD",
            ],
            "FormActionURL" => "https://secure-au.sandbox.ewaypayments.com/AccessCode/$accessCode",
            "CompleteCheckoutURL" => null,
            "Errors" => null,
        ]);

        $httpService = $this->prophet->prophesize('Eway\Rapid\Contract\HttpService');

        /** @var MethodProphecy $postAccessCodeSharedStub */
        $postAccessCodeSharedStub = $httpService->postAccessCodeShared(Argument::type('array'));
        $postAccessCodeSharedStub->withArguments([$mockTransaction])->willReturn($mockResponse)->shouldBeCalled();

        /** @var HttpService $stub */
        $stub = $httpService->reveal();
        $this->client->setHttpService($stub);


        $response = $this->client->updateCustomer(ApiMethod::RESPONSIVE_SHARED, $customer);

        $this->assertInstanceOf(CreateCustomerResponse::getClass(), $response);
        $this->assertTrue(is_array($response->getErrors()));
        $this->assertEmpty($response->getErrors());
        $this->assertEquals($accessCode, $response->AccessCode);
    }

    /**
     * Update Customer Token for Transparent Redirect Transaction
     */
    public function testUpdateCustomerTransparentRedirectShouldCallHttpServicePostAccessCode()
    {
        $customer = [
            'TokenCustomerID' => 987654321098,
            'Title' => 'Mr.',
            'FirstName' => 'John',
            'LastName' => 'Smith',
            'Country' => 'au',
            'RedirectUrl' => 'http://www.eway.com.au',
        ];

        $mockTransaction = [
            'Customer' => $customer,
            'Method' => PaymentMethod::UPDATE_TOKEN_CUSTOMER,
            'TransactionType' => TransactionType::MOTO,
            'RedirectUrl' => $customer['RedirectUrl'],
            'Payment' => [
                'TotalAmount' => 0,
            ],
            'Capture' => true,
        ];

        $accessCode = '60CF3YnOMi2x3PhqOKMzj8iRqsDcqEzWO7L6L-ROWPQ6jhlN4eqbYBaPkFGE8pCvI-6rERsoL1XRa_Xw7bCHx_YE3oyruET9HMTf281pOLelDuBJguwTqdE9tG_2TK-C1UDXZ';
        $mockResponse = $this->getResponse([
            "AccessCode" => $accessCode,
            "Customer" => [
                "CardNumber" => "444433XXXXXX1111",
                "CardStartMonth" => "",
                "CardStartYear" => "",
                "CardIssueNumber" => "",
                "CardName" => "John Smith",
                "CardExpiryMonth" => "12",
                "CardExpiryYear" => "21",
                "IsActive" => true,
                "TokenCustomerID" => $customer['TokenCustomerID'],
                "Reference" => "",
                "Title" => $customer['Title'],
                "FirstName" => $customer['FirstName'],
                "LastName" => $customer['LastName'],
                "CompanyName" => "",
                "JobDescription" => "",
                "Street1" => "",
                "Street2" => "",
                "City" => "",
                "State" => "",
                "PostalCode" => "",
                "Country" => $customer['Country'],
                "Email" => "",
                "Phone" => "",
                "Mobile" => "",
                "Comments" => "",
                "Fax" => "",
                "Url" => "",
            ],
            "Payment" => [
                "TotalAmount" => 0,
                "InvoiceNumber" => null,
                "InvoiceDescription" => null,
                "InvoiceReference" => null,
                "CurrencyCode" => "AUD",
            ],
            "FormActionURL" => "https://secure-au.sandbox.ewaypayments.com/AccessCode/$accessCode",
            "CompleteCheckoutURL" => null,
            "Errors" => null,
        ]);

        $httpService = $this->prophet->prophesize('Eway\Rapid\Contract\HttpService');

        /** @var MethodProphecy $postAccessCodeStub */
        $postAccessCodeStub = $httpService->postAccessCode(Argument::type('array'));
        $postAccessCodeStub->withArguments([$mockTransaction])->willReturn($mockResponse)->shouldBeCalled();

        /** @var HttpService $stub */
        $stub = $httpService->reveal();
        $this->client->setHttpService($stub);


        $response = $this->client->updateCustomer(ApiMethod::TRANSPARENT_REDIRECT, $customer);

        $this->assertInstanceOf(CreateCustomerResponse::getClass(), $response);
        $this->assertTrue(is_array($response->getErrors()));
        $this->assertEmpty($response->getErrors());
        $this->assertEquals($accessCode, $response->AccessCode);
    }
}
