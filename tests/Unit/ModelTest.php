<?php

namespace Eway\Test\Unit;

use Eway\Rapid\Enum\ShippingMethod;
use Eway\Rapid\Enum\TransactionType;
use Eway\Rapid\Model\Item;
use Eway\Rapid\Model\Payment;
use Eway\Rapid\Model\ShippingAddress;
use Eway\Rapid\Model\Transaction;
use Eway\Test\AbstractTest;
use PHPUnit_Framework_Error_Notice;

class ModelTest extends AbstractTest
{
    public function testToString()
    {
        $data = ['TotalAmount' => 100];
        $payment = new Payment($data);
        $this->assertEquals(json_encode($data), (string)$payment);
    }

    public function testItemWithTax()
    {
        $data = [
            "SKU" => "12345678901234567890",
            "Description" => "Item Description 1",
            "Quantity" => 1,
            "UnitCost" => 400,
            "Tax" => 100,
        ];
        $item = new Item($data);
        $this->assertEquals(500, $item->Total);
    }

    public function testItemWithoutTax()
    {
        $data = [
            "SKU" => "12345678901234567890",
            "Description" => "Item Description 1",
            "Quantity" => 2,
            "UnitCost" => 300,
        ];
        $item = new Item($data);
        $this->assertEquals(600, $item->Total);
    }

    public function testItemCalculate()
    {
        $data = [
            "SKU" => "12345678901234567890",
            "Description" => "Item Description 1",
            "Quantity" => 5,
            "UnitCost" => 100,
        ];
        $item = new Item($data);
        $item->calculate(500, 200, 3);
        $this->assertEquals(600, $item->Tax);
        $this->assertEquals(2100, $item->Total);
    }

    public function testEmptyItem()
    {
        $item = new Item();
        $this->assertCount(0, $item->toArray());
    }

    public function testShippingAddress()
    {
        $data = [
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
        $shippingAddress = new ShippingAddress($data);
        $shippingAddress->ShippingMethod = null;
        $this->assertEquals(ShippingMethod::UNKNOWN, $shippingAddress->ShippingMethod);
    }

    public function testUnset()
    {
        $data = [
            'TotalAmount' => 100,
            'InvoiceNumber' => 'foo',
        ];
        $payment = new Payment($data);
        $this->assertEquals(json_encode($data), (string)$payment);
        unset($payment->InvoiceNumber);
        unset($data['InvoiceNumber']);
        $this->assertEquals(json_encode($data), (string)$payment);
    }

    /**
     * @expectedException PHPUnit_Framework_Error_Notice
     */
    public function testInvalidAttribute()
    {
        $data = ['TotalAmount' => 100];
        $payment = new Payment($data);
        $this->assertNull($payment->Foo);
    }

    public function testInvalidAttributeReturnNull()
    {
        $originErrorReporting = ini_get('error_reporting');
        ini_set('error_reporting', E_ALL ^ E_USER_NOTICE);


        $data = ['TotalAmount' => 100];
        $payment = new Payment($data);
        $this->assertNull($payment->Foo);


        ini_set('error_reporting', $originErrorReporting);
    }

    public function testRecursiveToArray()
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
        $transactionData = [
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
        $transaction = new Transaction($transactionData);
        $toArray = $transaction->toArray();
        $this->assertArrayHasKey('Customer', $toArray);
        $this->assertArrayHasKey('CardDetails', $toArray['Customer']);
        $this->assertArrayHasKey('Name', $toArray['Customer']['CardDetails']);
    }

    /**
     * @dataProvider provideTransactionCaptureData
     *
     * @param $data
     * @param $expected
     */
    public function testTransactionDefaultCapture($data, $expected)
    {
        $transaction = new Transaction($data);
        $this->assertEquals($expected, $transaction->Capture);
    }

    /**
     * @return array
     */
    public function provideTransactionCaptureData()
    {
        return [
            [[], true],
            [['Capture' => true], true],
            [['Capture' => false], false],
        ];
    }
}
