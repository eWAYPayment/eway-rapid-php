<?php

namespace Eway\Test\Unit;

use Eway\Rapid\Contract\HttpService as HttpServiceContract;
use Eway\Rapid\Enum\PaymentMethod;
use Eway\Rapid\Enum\TransactionType;
use Eway\Rapid\Service\Http;
use Eway\Rapid\Service\Http\Response;
use InvalidArgumentException;

/**
 * Class ServiceTest.
 */
class ServiceTest extends AbstractHttpTest
{
    /**
     * @var string
     */
    protected $key;

    /**
     * @var string
     */
    protected $password;

    /**
     * @var string
     */
    protected $endpoint;

    /**
     * @var Http
     */
    protected $service;

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     */
    public function setUp()
    {
        parent::setUp();
        $this->key = 'foo';
        $this->password = 'bar';
        $this->endpoint = sprintf('http://%s:%s/', self::$dns, self::$port);
        $this->service = new Http($this->key, $this->password, $this->endpoint);
    }


    public function testGetTransaction()
    {
        $reference = '12345678';
        $body = '{"Transactions":[{"AuthorisationCode":"123456","ResponseCode":"00","ResponseMessage":"A2000","InvoiceNumber":"","InvoiceReference":"","TotalAmount":1000,"TransactionID":12345678,"TransactionStatus":true,"TokenCustomerID":null,"BeagleScore":0,"Options":[],"Verification":{"CVN":0,"Address":0,"Email":0,"Mobile":0,"Phone":0},"BeagleVerification":{"Email":0,"Phone":0},"Customer":{"TokenCustomerID":null,"Reference":null,"Title":null,"FirstName":"","LastName":"","CompanyName":null,"JobDescription":null,"Street1":"","Street2":"","City":"","State":"","PostalCode":"","Country":"","Email":"","Phone":"","Mobile":null,"Comments":null,"Fax":null,"Url":null},"CustomerNote":null,"ShippingAddress":{"ShippingMethod":"Unknown","FirstName":"","LastName":"","Street1":"","Street2":"","City":"","State":"","Country":"","PostalCode":"","Email":"","Phone":"","Fax":null}}],"Errors":""}';
        $uri = $this->service->getUri([
            HttpServiceContract::API_TRANSACTION_QUERY,
            ['Reference' => $reference],
        ], false);


        $this->http->mock
            ->when()
            ->methodIs('GET')
            ->pathIs('/'.$uri)
            ->then()
            ->body($body)
            ->end();
        $this->http->setUp();


        $response = $this->service->getTransaction($reference);
        $this->assertInstanceOf(Response::getClass(), $response);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertSame($body, $response->getBody());
    }

    public function testGetTransactionInvoiceNumber()
    {
        $invoiceNumber = 'invoice_number_1234567890';
        $body = '{"Transactions":[{"AuthorisationCode":"123456","ResponseCode":"00","ResponseMessage":"A2000","InvoiceNumber":"invoice_number_1234567890","InvoiceReference":"","TotalAmount":1000,"TransactionID":12345678,"TransactionStatus":true,"TokenCustomerID":null,"BeagleScore":0,"Options":[],"Verification":{"CVN":0,"Address":0,"Email":0,"Mobile":0,"Phone":0},"BeagleVerification":{"Email":0,"Phone":0},"Customer":{"TokenCustomerID":null,"Reference":null,"Title":null,"FirstName":"","LastName":"","CompanyName":null,"JobDescription":null,"Street1":"","Street2":"","City":"","State":"","PostalCode":"","Country":"","Email":"","Phone":"","Mobile":null,"Comments":null,"Fax":null,"Url":null},"CustomerNote":null,"ShippingAddress":{"ShippingMethod":"Unknown","FirstName":"","LastName":"","Street1":"","Street2":"","City":"","State":"","Country":"","PostalCode":"","Email":"","Phone":"","Fax":null}}],"Errors":""}';
        $uri = $this->service->getUri([
            HttpServiceContract::API_TRANSACTION_INVOICE_NUMBER_QUERY,
            ['InvoiceNumber' => $invoiceNumber],
        ], false);


        $this->http->mock
            ->when()
            ->methodIs('GET')
            ->pathIs('/'.$uri)
            ->then()
            ->body($body)
            ->end();
        $this->http->setUp();


        $response = $this->service->getTransactionInvoiceNumber($invoiceNumber);
        $this->assertInstanceOf(Response::getClass(), $response);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertSame($body, $response->getBody());
    }

    public function testGetTransactionInvoiceReference()
    {
        $invoiceReference = '1234567890';
        $body = '{"Transactions":[{"AuthorisationCode":"123456","ResponseCode":"00","ResponseMessage":"A2000","InvoiceNumber":"","InvoiceReference":"1234567890","TotalAmount":1000,"TransactionID":12345678,"TransactionStatus":true,"TokenCustomerID":null,"BeagleScore":0,"Options":[],"Verification":{"CVN":0,"Address":0,"Email":0,"Mobile":0,"Phone":0},"BeagleVerification":{"Email":0,"Phone":0},"Customer":{"TokenCustomerID":null,"Reference":null,"Title":null,"FirstName":"","LastName":"","CompanyName":null,"JobDescription":null,"Street1":"","Street2":"","City":"","State":"","PostalCode":"","Country":"","Email":"","Phone":"","Mobile":null,"Comments":null,"Fax":null,"Url":null},"CustomerNote":null,"ShippingAddress":{"ShippingMethod":"Unknown","FirstName":"","LastName":"","Street1":"","Street2":"","City":"","State":"","Country":"","PostalCode":"","Email":"","Phone":"","Fax":null}}],"Errors":""}';
        $uri = $this->service->getUri([
            HttpServiceContract::API_TRANSACTION_INVOICE_REFERENCE_QUERY,
            ['InvoiceReference' => $invoiceReference],
        ], false);


        $this->http->mock
            ->when()
            ->methodIs('GET')
            ->pathIs('/'.$uri)
            ->then()
            ->body($body)
            ->end();
        $this->http->setUp();


        $response = $this->service->getTransactionInvoiceReference($invoiceReference);
        $this->assertInstanceOf(Response::getClass(), $response);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertSame($body, $response->getBody());
    }

    public function testPostTransaction()
    {
        $transaction = [
            'Customer' => [
                'CardDetails' => [
                    'Name' => 'John Smith',
                    'Number' => '4444333322221111',
                    'ExpiryMonth' => '12',
                    'ExpiryYear' => '25',
                    'CVN' => '123',
                ],
            ],
            'Payment' => [
                'TotalAmount' => 1000,
            ],
            'TransactionType' => TransactionType::PURCHASE,
            'Method' => PaymentMethod::PROCESS_PAYMENT,
        ];
        $body = '{"AuthorisationCode":"123456","ResponseCode":"00","ResponseMessage":"A2000","TransactionID":12345678,"TransactionStatus":true,"TransactionType":"Purchase","BeagleScore":0,"Verification":{"CVN":0,"Address":0,"Email":0,"Mobile":0,"Phone":0},"Customer":{"CardDetails":{"Number":"444433XXXXXX1111","Name":"John Smith","ExpiryMonth":"12","ExpiryYear":"25","StartMonth":null,"StartYear":null,"IssueNumber":null},"TokenCustomerID":null,"Reference":"","Title":"Mr.","FirstName":"","LastName":"","CompanyName":"","JobDescription":"","Street1":"","Street2":"","City":"","State":"","PostalCode":"","Country":"","Email":"","Phone":"","Mobile":"","Comments":"","Fax":"","Url":""},"Payment":{"TotalAmount":1000,"InvoiceNumber":"","InvoiceDescription":"","InvoiceReference":"","CurrencyCode":"AUD"},"Errors":""}';
        $uri = $this->service->getUri(HttpServiceContract::API_TRANSACTION, false);


        $this->http->mock
            ->when()
            ->methodIs('POST')
            ->pathIs('/'.$uri)
            ->then()
            ->body($body)
            ->end();
        $this->http->setUp();


        $response = $this->service->postTransaction($transaction);
        $this->assertInstanceOf(Response::getClass(), $response);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertSame($body, $response->getBody());
    }

    public function testPostTransactionRefund()
    {
        $transactionId = '12345678';
        $refund = [
            'Refund' => [
                'TransactionID' => $transactionId,
                'TotalAmount' => 1000,
            ],
        ];
        $body = '{"AuthorisationCode":"123456","ResponseCode":null,"ResponseMessage":"A2000","TransactionID":12345678,"TransactionStatus":true,"Verification":null,"Customer":{"CardDetails":{"Number":null,"Name":null,"ExpiryMonth":null,"ExpiryYear":null,"StartMonth":null,"StartYear":null,"IssueNumber":null},"TokenCustomerID":null,"Reference":null,"Title":null,"FirstName":"","LastName":"","CompanyName":null,"JobDescription":null,"Street1":"","Street2":null,"City":"","State":"","PostalCode":"","Country":"","Email":"","Phone":"","Mobile":null,"Comments":null,"Fax":null,"Url":null},"Refund":{"TransactionID":"12345679","TotalAmount":1000,"InvoiceNumber":null,"InvoiceDescription":"","InvoiceReference":"","CurrencyCode":null},"Errors":""}';
        $uri = $this->service->getUri(
            [HttpServiceContract::API_TRANSACTION_REFUND,
            ['TransactionID' => $transactionId]],
            false
        );


        $this->http->mock
            ->when()
            ->methodIs('POST')
            ->pathIs('/'.$uri)
            ->then()
            ->body($body)
            ->end();
        $this->http->setUp();


        $response = $this->service->postTransactionRefund($transactionId, $refund);
        $this->assertInstanceOf(Response::getClass(), $response);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertSame($body, $response->getBody());
    }

    public function testPostAccessCodeShared()
    {
        $transaction = [
            "Payment" => [
                "TotalAmount" => 100,
            ],
            "RedirectUrl" => "http://www.eway.com.au",
            "CancelUrl" => "http://www.eway.com.au",
            "TransactionType" => TransactionType::PURCHASE,
            "Method" => PaymentMethod::PROCESS_PAYMENT,
        ];
        $accessCode = '60CF3Rv-w6jZvyE7jhZ8XMv19TpH8J5GmoilPq5ina-i2p4l9YU2ZMPxOLmDPWl-1AX53mwyZBH3NYnXv0F6vjabRdqfdA1FANRjU-_KVSzDasniRTn9EsZmL6Bnvua77zFs4';
        $body = '{"SharedPaymentUrl":"https:\/\/secure-au.sandbox.ewaypayments.com\/sharedpage\/sharedpayment?AccessCode='.$accessCode.'","AccessCode":"'.$accessCode.'","Customer":{"CardNumber":"","CardStartMonth":"","CardStartYear":"","CardIssueNumber":"","CardName":"","CardExpiryMonth":"","CardExpiryYear":"","IsActive":false,"TokenCustomerID":null,"Reference":"","Title":"Mr.","FirstName":"","LastName":"","CompanyName":"","JobDescription":"","Street1":"","Street2":"","City":"","State":"","PostalCode":"","Country":"","Email":"","Phone":"","Mobile":"","Comments":"","Fax":"","Url":""},"Payment":{"TotalAmount":100,"InvoiceNumber":null,"InvoiceDescription":null,"InvoiceReference":null,"CurrencyCode":"AUD"},"FormActionURL":"https:\/\/secure-au.sandbox.ewaypayments.com\/AccessCode\/'.$accessCode.'","CompleteCheckoutURL":null,"Errors":""}';
        $uri = $this->service->getUri(HttpServiceContract::API_ACCESS_CODE_SHARED, false);


        $this->http->mock
            ->when()
            ->methodIs('POST')
            ->pathIs('/'.$uri)
            ->then()
            ->body($body)
            ->end();
        $this->http->setUp();


        $response = $this->service->postAccessCodeShared($transaction);
        $this->assertInstanceOf(Response::getClass(), $response);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertSame($body, $response->getBody());
    }

    public function testGetAccessCode()
    {
        $accessCode = 'A10012yIV2-MEEfkk7b7oYZqtulwNHv2dAFLv7T2guZEpjwBMHJoU-KxQihXVV10unFYbOUJ9Ob58oALLxn88_rzWDJhyq1-qW_hZ-xYjS3kdsCSNLtFHVESfDRVPWZqisLto';
        $body = '{"AccessCode":"'.$accessCode.'","AuthorisationCode":null,"ResponseCode":null,"ResponseMessage":"","InvoiceNumber":"","InvoiceReference":"","TotalAmount":0,"TransactionID":null,"TransactionStatus":false,"TokenCustomerID":null,"BeagleScore":null,"Options":[],"Verification":{"CVN":0,"Address":0,"Email":0,"Mobile":0,"Phone":0},"BeagleVerification":{"Email":0,"Phone":0},"Errors":null}';
        $uri = $this->service->getUri([
            HttpServiceContract::API_ACCESS_CODE_QUERY,
            ['AccessCode' => $accessCode],
        ], false);


        $this->http->mock
            ->when()
            ->methodIs('GET')
            ->pathIs('/'.$uri)
            ->then()
            ->body($body)
            ->end();
        $this->http->setUp();


        $response = $this->service->getAccessCode($accessCode);
        $this->assertInstanceOf(Response::getClass(), $response);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertSame($body, $response->getBody());
    }

    public function testPostAccessCode()
    {
        $transaction = [
            "Payment" => [
                "TotalAmount" => 100,
            ],
            "RedirectUrl" => "http://www.eway.com.au",
            "TransactionType" => TransactionType::PURCHASE,
            "Method" => PaymentMethod::PROCESS_PAYMENT,
        ];
        $accessCode = '60CF33rLmr-_zJizfEyRX954p6zUlQX5pJB6P_0xGelmrWYRHbEYkTqMPAl7jt5AngA0UEJ3B_lL9kRTvZdEdyY7ZMysI4V76oUw28h4Tih0Yv9rPNIgfeCJhvFD_xjqNLZ1x';
        $body = '{"AccessCode":"'.$accessCode.'","Customer":{"CardNumber":"","CardStartMonth":"","CardStartYear":"","CardIssueNumber":"","CardName":"","CardExpiryMonth":"","CardExpiryYear":"","IsActive":false,"TokenCustomerID":null,"Reference":"","Title":"Mr.","FirstName":"","LastName":"","CompanyName":"","JobDescription":"","Street1":"","Street2":"","City":"","State":"","PostalCode":"","Country":"","Email":"","Phone":"","Mobile":"","Comments":"","Fax":"","Url":""},"Payment":{"TotalAmount":100,"InvoiceNumber":null,"InvoiceDescription":null,"InvoiceReference":null,"CurrencyCode":"AUD"},"FormActionURL":"https:\/\/secure-au.sandbox.ewaypayments.com\/AccessCode\/'.$accessCode.'","CompleteCheckoutURL":null,"Errors":""}';
        $uri = $this->service->getUri(HttpServiceContract::API_ACCESS_CODE, false);


        $this->http->mock
            ->when()
            ->methodIs('POST')
            ->pathIs('/'.$uri)
            ->then()
            ->body($body)
            ->end();
        $this->http->setUp();


        $response = $this->service->postAccessCode($transaction);
        $this->assertInstanceOf(Response::getClass(), $response);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertSame($body, $response->getBody());
    }

    public function testGetCustomer()
    {
        $tokenCustomerId = '987654321098';
        $body = '{"Customers":[{"CardDetails":{"Number":"444433XXXXXX1111","Name":"John Smith4","ExpiryMonth":"12","ExpiryYear":"25","StartMonth":"","StartYear":"","IssueNumber":""},"TokenCustomerID":'.$tokenCustomerId.',"Reference":"","Title":"Mr.","FirstName":"John4","LastName":"Smith4","CompanyName":"","JobDescription":"","Street1":"","Street2":null,"City":"","State":"","PostalCode":"","Country":"au","Email":"","Phone":"","Mobile":"","Comments":"","Fax":"","Url":""}],"Errors":""}';
        $uri = $this->service->getUri([
            HttpServiceContract::API_CUSTOMER_QUERY,
            ['TokenCustomerID' => $tokenCustomerId],
        ], false);


        $this->http->mock
            ->when()
            ->methodIs('GET')
            ->pathIs('/'.$uri)
            ->then()
            ->body($body)
            ->end();
        $this->http->setUp();


        $response = $this->service->getCustomer($tokenCustomerId);
        $this->assertInstanceOf(Response::getClass(), $response);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertSame($body, $response->getBody());
    }

    public function testPostCapturePayment()
    {
        $transaction = [
            'Payment' => [
                'TotalAmount' => 1000,
            ],
            'TransactionID' => 12345678,
        ];
        $body = '{"ResponseCode":"123456","ResponseMessage":"234567","TransactionID":12345679,"TransactionStatus":true,"Errors":""}';
        $uri = $this->service->getUri(HttpServiceContract::API_CAPTURE_PAYMENT, false);


        $this->http->mock
            ->when()
            ->methodIs('POST')
            ->pathIs('/'.$uri)
            ->then()
            ->body($body)
            ->end();
        $this->http->setUp();


        $response = $this->service->postCapturePayment($transaction);
        $this->assertInstanceOf(Response::getClass(), $response);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertSame($body, $response->getBody());
    }

    public function testPostCancelAuthorisation()
    {
        $refund = [
            'TransactionID' => 12345678,
        ];
        $body = '{"ResponseCode":"123456","ResponseMessage":"234567","TransactionID":12345679,"TransactionStatus":true,"Errors":""}';
        $uri = $this->service->getUri(HttpServiceContract::API_CANCEL_AUTHORISATION, false);


        $this->http->mock
            ->when()
            ->methodIs('POST')
            ->pathIs('/'.$uri)
            ->then()
            ->body($body)
            ->end();
        $this->http->setUp();


        $response = $this->service->postCancelAuthorisation($refund);
        $this->assertInstanceOf(Response::getClass(), $response);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertSame($body, $response->getBody());
    }

    public function testGetSettlementSearch()
    {
        $search = [
            'ReportMode' => 'Both',
            'SettlementDate' => '2015-02-02',
        ];
        $body = '{"SettlementSummaries":[{"SettlementID":"53e78b14-ac2c-4b1b-a099-a12c6d5f30bc","Currency":"36","CurrencyCode":"AUD","TotalCredit":97100,"TotalDebit":320,"TotalBalance":96780,"BalancePerCardType":[{"CardType":"VI","NumberOfTransactions":14,"Credit":97100,"Debit":320,"Balance":96780}]}],"SettlementTransactions":[{"SettlementID":"53e78b14-ac2c-4b1b-a099-a12c6d5f30bc","CurrencyCardTypeTransactionID":"36:VI:11258912","eWAYCustomerID":91312168,"Currency":"36","CurrencyCode":"AUD","TransactionID":11258912,"TxnReference":"0000000011258912","CardType":"VI","Amount":100,"TransactionType":"1","TransactionDateTime":"\/Date(1422795600000)\/","SettlementDateTime":"\/Date(1422795600000)\/"},{"SettlementID":"53e78b14-ac2c-4b1b-a099-a12c6d5f30bc","CurrencyCardTypeTransactionID":"36:VI:11259196","eWAYCustomerID":91312168,"Currency":"36","CurrencyCode":"AUD","TransactionID":11259196,"TxnReference":"0000000011259196","CardType":"VI","Amount":1000,"TransactionType":"1","TransactionDateTime":"\/Date(1422795600000)\/","SettlementDateTime":"\/Date(1422795600000)\/"},{"SettlementID":"53e78b14-ac2c-4b1b-a099-a12c6d5f30bc","CurrencyCardTypeTransactionID":"36:VI:11259550","eWAYCustomerID":91312168,"Currency":"36","CurrencyCode":"AUD","TransactionID":11259550,"TxnReference":"0000000011259550","CardType":"VI","Amount":1000,"TransactionType":"1","TransactionDateTime":"\/Date(1422795600000)\/","SettlementDateTime":"\/Date(1422795600000)\/"},{"SettlementID":"53e78b14-ac2c-4b1b-a099-a12c6d5f30bc","CurrencyCardTypeTransactionID":"36:VI:11259580","eWAYCustomerID":91312168,"Currency":"36","CurrencyCode":"AUD","TransactionID":11259580,"TxnReference":"0000000011259580","CardType":"VI","Amount":1000,"TransactionType":"1","TransactionDateTime":"\/Date(1422795600000)\/","SettlementDateTime":"\/Date(1422795600000)\/"},{"SettlementID":"53e78b14-ac2c-4b1b-a099-a12c6d5f30bc","CurrencyCardTypeTransactionID":"36:VI:11259679","eWAYCustomerID":91312168,"Currency":"36","CurrencyCode":"AUD","TransactionID":11259679,"TxnReference":"0000000011259679","CardType":"VI","Amount":10,"TransactionType":"4","TransactionDateTime":"\/Date(1422795600000)\/","SettlementDateTime":"\/Date(1422795600000)\/"},{"SettlementID":"53e78b14-ac2c-4b1b-a099-a12c6d5f30bc","CurrencyCardTypeTransactionID":"36:VI:11259690","eWAYCustomerID":91312168,"Currency":"36","CurrencyCode":"AUD","TransactionID":11259690,"TxnReference":"0000000011259690","CardType":"VI","Amount":10,"TransactionType":"4","TransactionDateTime":"\/Date(1422795600000)\/","SettlementDateTime":"\/Date(1422795600000)\/"},{"SettlementID":"53e78b14-ac2c-4b1b-a099-a12c6d5f30bc","CurrencyCardTypeTransactionID":"36:VI:11259691","eWAYCustomerID":91312168,"Currency":"36","CurrencyCode":"AUD","TransactionID":11259691,"TxnReference":"0000000011259691","CardType":"VI","Amount":100,"TransactionType":"4","TransactionDateTime":"\/Date(1422795600000)\/","SettlementDateTime":"\/Date(1422795600000)\/"},{"SettlementID":"53e78b14-ac2c-4b1b-a099-a12c6d5f30bc","CurrencyCardTypeTransactionID":"36:VI:11259710","eWAYCustomerID":91312168,"Currency":"36","CurrencyCode":"AUD","TransactionID":11259710,"TxnReference":"0000000011259710","CardType":"VI","Amount":100,"TransactionType":"4","TransactionDateTime":"\/Date(1422795600000)\/","SettlementDateTime":"\/Date(1422795600000)\/"},{"SettlementID":"53e78b14-ac2c-4b1b-a099-a12c6d5f30bc","CurrencyCardTypeTransactionID":"36:VI:11259714","eWAYCustomerID":91312168,"Currency":"36","CurrencyCode":"AUD","TransactionID":11259714,"TxnReference":"0000000011259714","CardType":"VI","Amount":100,"TransactionType":"4","TransactionDateTime":"\/Date(1422795600000)\/","SettlementDateTime":"\/Date(1422795600000)\/"},{"SettlementID":"53e78b14-ac2c-4b1b-a099-a12c6d5f30bc","CurrencyCardTypeTransactionID":"36:VI:11260829","eWAYCustomerID":91312168,"Currency":"36","CurrencyCode":"AUD","TransactionID":11260829,"TxnReference":"0000000011260829","CardType":"VI","Amount":90000,"TransactionType":"1","TransactionDateTime":"\/Date(1422795600000)\/","SettlementDateTime":"\/Date(1422795600000)\/"},{"SettlementID":"53e78b14-ac2c-4b1b-a099-a12c6d5f30bc","CurrencyCardTypeTransactionID":"36:VI:11260840","eWAYCustomerID":91312168,"Currency":"36","CurrencyCode":"AUD","TransactionID":11260840,"TxnReference":"0000000011260840","CardType":"VI","Amount":1000,"TransactionType":"8","TransactionDateTime":"\/Date(1422795600000)\/","SettlementDateTime":"\/Date(1422795600000)\/"},{"SettlementID":"53e78b14-ac2c-4b1b-a099-a12c6d5f30bc","CurrencyCardTypeTransactionID":"36:VI:11260888","eWAYCustomerID":91312168,"Currency":"36","CurrencyCode":"AUD","TransactionID":11260888,"TxnReference":"0000000011260888","CardType":"VI","Amount":1000,"TransactionType":"8","TransactionDateTime":"\/Date(1422795600000)\/","SettlementDateTime":"\/Date(1422795600000)\/"},{"SettlementID":"53e78b14-ac2c-4b1b-a099-a12c6d5f30bc","CurrencyCardTypeTransactionID":"36:VI:11261122","eWAYCustomerID":91312168,"Currency":"36","CurrencyCode":"AUD","TransactionID":11261122,"TxnReference":"0000000011261122","CardType":"VI","Amount":1000,"TransactionType":"1","TransactionDateTime":"\/Date(1422795600000)\/","SettlementDateTime":"\/Date(1422795600000)\/"},{"SettlementID":"53e78b14-ac2c-4b1b-a099-a12c6d5f30bc","CurrencyCardTypeTransactionID":"36:VI:11261127","eWAYCustomerID":91312168,"Currency":"36","CurrencyCode":"AUD","TransactionID":11261127,"TxnReference":"0000000011261127","CardType":"VI","Amount":1000,"TransactionType":"1","TransactionDateTime":"\/Date(1422795600000)\/","SettlementDateTime":"\/Date(1422795600000)\/"}],"Errors":""}';
        $uri = $this->service->getUri(HttpServiceContract::API_SETTLEMENT_SEARCH, false);

        $uri .= '?'.http_build_query($search);

        $this->http->mock
            ->when()
            ->methodIs('GET')
            ->pathIs('/'.$uri)
            ->then()
            ->body($body)
            ->end();
        $this->http->setUp();

        $response = $this->service->getSettlementSearch($search);
        $this->assertInstanceOf(Response::getClass(), $response);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertSame($body, $response->getBody());
    }

    /**
     * @dataProvider provideUri
     *
     * @param $uri
     * @param $expected
     */
    public function testGetUriWithValidUri($uri, $expected)
    {
        $endpoint = 'http://localhost';
        $service = new Http('', '', $endpoint);
        $this->assertEquals($endpoint.$expected, $service->getUri($uri));
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testGetUriWithInvalidUri()
    {
        $this->service->getUri(['foo', 'bar']);
    }

    /**
     * @return array
     */
    public function provideUri()
    {
        return [
            ['/foo', '/foo'],
            [['/foo/{bar}', ['bar' => 'baz']], '/foo/baz'],
        ];
    }
}
