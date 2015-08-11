<?php

namespace Eway\Test\Unit;

use Eway\Rapid\Enum\ApiMethod;
use Eway\Rapid\Enum\PaymentMethod;
use Eway\Rapid\Enum\TransactionType;
use Eway\Rapid\Model\Payment;
use Eway\Rapid\Validator\ClassValidator;
use Eway\Rapid\Validator\EnumValidator;
use Eway\Test\AbstractTest;
use InvalidArgumentException;

/**
 * Class ValidatorTest.
 */
class ValidatorTest extends AbstractTest
{
    /**
     * @dataProvider provideClassValidatorData
     *
     * @param $class
     * @param $data
     */
    public function testClassValidator($class, $data)
    {
        $this->assertInstanceOf($class, ClassValidator::getInstance($class, $data));
    }

    /**
     * @dataProvider provideEnumValidatorData
     *
     * @param $class
     * @param $field
     * @param $expected
     */
    public function testEnumValidator($class, $field, $expected)
    {
        $this->assertEquals($expected, EnumValidator::validate($class, $field, $expected));
    }

    /**
     * @dataProvider provideInvalidEnumValidatorData
     * @expectedException InvalidArgumentException
     *
     * @param $class
     * @param $field
     * @param $value
     */
    public function testEnumValidatorWithInvalidData($class, $field, $value)
    {
        EnumValidator::validate($class, $field, $value);
    }

    public function provideClassValidatorData()
    {
        return [
            [Payment::getClass(), ['TotalAmount' => 100]],
            [Payment::getClass(), new Payment(['TotalAmount' => 100])],
        ];
    }

    public function provideEnumValidatorData()
    {
        return [
            [ApiMethod::getClass(), 'foo', 'Direct'],
            [PaymentMethod::getClass(), 'bar', 'ProcessPayment'],
        ];
    }

    public function provideInvalidEnumValidatorData()
    {
        return [
            [TransactionType::getClass(), 'fuz', 'baz'],
            [Payment::getClass(), 'x', 'y'],
        ];
    }
}
