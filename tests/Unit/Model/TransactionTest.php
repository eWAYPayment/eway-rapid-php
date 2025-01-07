<?php

declare(strict_types=1);

namespace Eway\Test\Unit\Model;

use Eway\Rapid\Model\Customer;
use Eway\Rapid\Model\Payment;
use Eway\Rapid\Model\Transaction;
use PHPUnit\Framework\TestCase;

class TransactionTest extends TestCase
{
    /**
     * @dataProvider captureDataProvider
     * @param bool $expected
     * @param array $data
     * @return void
     */
    public function testCapture(bool $expected, array $data): void
    {
        $transaction = new Transaction($data);
        $this->assertEquals($expected, $transaction->Capture);
    }

    /**
     * @return array[]
     */
    public function captureDataProvider(): array
    {
        return [
            [true, []],
            [true, ['Capture' => true]],
            [false, ['Capture' => false]],
        ];
    }

    /**
     * @dataProvider customerDataProvider
     * @param Customer|null $customer
     * @return void
     */
    public function testSetCustomerAttribute(?Customer $customer): void
    {
        $transaction = new Transaction();
        $transaction->setCustomerAttribute($customer);
        $this->assertEquals($customer, $transaction->Customer);
    }

    /**
     * @return array
     */
    public function customerDataProvider(): array
    {
        return [
            [new Customer()],
            [null],
        ];
    }

    /**
     * @return void
     */
    public function testIsFillable(): void
    {
        $this->assertFalse((new Transaction())->isFillable('Attribute'));
    }

    /**
     * @return void
     */
    public function testSetAttribute(): void
    {
        $payment = new Payment();
        $transaction = new Transaction();
        $this->assertEquals($transaction, $transaction->setAttribute('Payment', $payment));
        $this->assertEquals($payment, $transaction->Payment);
    }

    /**
     * @return void
     */
    public function testUnsetAttribute(): void
    {
        $transaction = new Transaction(['Payment' => new Payment()]);
        $transaction->__unset('Payment');

        $this->assertObjectNotHasProperty('Payment', $transaction);
        $this->assertNull($transaction->getAttribute('Payment'));
    }

    /**
     * @return void
     */
    public function testGetUndefinedProperty(): void
    {
        $transaction = new Transaction([]);
        $this->assertNull($transaction->getAttribute('UndefinedProps'));
    }

    /**
     * @return void
     */
    public function testAttributesToArray(): void
    {
        $transaction = new Transaction();
        $transaction->setAttribute('Attr', ['Value' => 1]);

        $this->assertEquals(
            [
                'Capture' => true,
                'Attr' => [
                    'Value' => 1,
                ],
            ],
            $transaction->attributesToArray(),
        );
    }
}






























