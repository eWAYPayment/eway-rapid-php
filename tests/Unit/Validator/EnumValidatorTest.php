<?php

declare(strict_types=1);

namespace Eway\Test\Unit\Validator;

use Eway\Rapid\Enum\ApiMethod;
use Eway\Rapid\Enum\LogLevel;
use Eway\Rapid\Validator\EnumValidator;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use stdClass;

class EnumValidatorTest extends TestCase
{
    /**
     * @return void
     */
    public function testValidate(): void
    {
        $this->assertEquals(
            ApiMethod::DIRECT,
            EnumValidator::validate(
                ApiMethod::class,
                ApiMethod::DIRECT,
                ApiMethod::DIRECT,
            )
        );
    }

    /**
     * @dataProvider validateDataProvider
     * @param string $class
     * @param string $field
     * @param mixed $value
     * @return void
     */
    public function testValidateInvalid(string $class, string $field, $value): void
    {
        $this->expectException(InvalidArgumentException::class);
        EnumValidator::validate($class, $field, $value);
    }

    /**
     * @return array[]
     */
    public function validateDataProvider(): array
    {
        return [
            [stdClass::class, 'Attr', 'value'],
            [LogLevel::class, LogLevel::DEBUG, 'value'],
        ];
    }
}
