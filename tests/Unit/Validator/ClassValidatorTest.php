<?php

declare(strict_types=1);

namespace Eway\Test\Unit\Validator;

use Eway\Rapid\Model\Customer;
use Eway\Rapid\Model\Refund;
use Eway\Rapid\Validator\ClassValidator;
use PHPUnit\Framework\TestCase;

class ClassValidatorTest extends TestCase
{
    /**
     * @dataProvider instanceDataProvider
     * @param mixed $expected
     * @param mixed $class
     * @param mixed $instance
     * @return void
     */
    public function testGetInstance($expected, $class, $instance): void
    {
        $this->assertEquals($expected, ClassValidator::getInstance($class, $instance));
    }

    /**
     * @return array[]
     */
    public function instanceDataProvider(): array
    {
        $customer = new Customer();

        return [
            [$customer, Customer::class, $customer],
            [new Refund(), Refund::class, []],
        ];
    }
}
