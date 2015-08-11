<?php

namespace Eway\Test\Unit;

use Eway\Rapid\Enum\ApiMethod;
use Eway\Rapid\Enum\BeagleVerifyStatus;
use Eway\Rapid\Enum\FraudAction;
use Eway\Rapid\Enum\PaymentMethod;
use Eway\Rapid\Enum\ShippingMethod;
use Eway\Rapid\Enum\TransactionType;
use Eway\Rapid\Enum\VerifyStatus;
use Eway\Test\AbstractTest;

/**
 * Class EnumTest.
 */
class EnumTest extends AbstractTest
{
    /**
     * @dataProvider provideValidValues
     *
     * @param $class
     * @param $value
     * @param $strict
     */
    public function testIsValidValue($class, $value, $strict)
    {
        $this->assertTrue(call_user_func_array($class.'::isValidValue', [$value, $strict]));
    }

    /**
     * @dataProvider provideInvalidValues
     *
     * @param $class
     * @param $value
     * @param $strict
     */
    public function testInvalidValue($class, $value, $strict)
    {
        $this->assertFalse(call_user_func_array($class.'::isValidValue', [$value, $strict]));
    }

    /**
     * @dataProvider provideValidationMessage
     *
     * @param $class
     * @param $name
     * @param $expected
     */
    public function testGetValidationMessage($class, $name, $expected)
    {
        $this->assertEquals($expected, call_user_func_array($class.'::getValidationMessage', [$name]));
    }

    public function testGetOptions()
    {
        $options = [
            'UNCHECKED' => 0,
            'VALID' => 1,
            'INVALID' => 2,
        ];
        $this->assertEquals($options, VerifyStatus::getOptionsArray());
    }

    public function provideValidValues()
    {
        return [
            [ApiMethod::getClass(), 'Direct', true],
            [BeagleVerifyStatus::getClass(), '0', false],
        ];
    }

    public function provideInvalidValues()
    {
        return [
            [FraudAction::getClass(), '0', true],
            [PaymentMethod::getClass(), 'foo', true],
            [ShippingMethod::getClass(), 'bar', false],
        ];
    }

    public function provideValidationMessage()
    {
        return [
            [
                TransactionType::getClass(),
                'TransactionType',
                'TransactionType must be one of the following: '.implode(', ', [
                    TransactionType::PURCHASE,
                    TransactionType::RECURRING,
                    TransactionType::MOTO,
                ]),
            ],
        ];
    }
}
