<?php

declare(strict_types=1);

namespace Eway\Test\Unit\Model;

use Eway\Rapid\Enum\VerifyStatus;
use Eway\Rapid\Model\Verification;
use PHPUnit\Framework\TestCase;

class VerificationTest extends TestCase
{
    /**
     * @dataProvider attributesDataProvider
     * @param mixed $value
     * @param string $field
     * @return void
     */
    public function testSetAttributes($value, string $field): void
    {
        $method = sprintf('set%sAttribute', $field);
        $verification = new Verification();
        $verification->{$method}($value);
        $this->assertEquals($value, $verification->{$field});
    }

    /**
     * @return array[]
     */
    public function attributesDataProvider(): array
    {
        return [
            [VerifyStatus::VALID, 'CVN'],
            [VerifyStatus::VALID, 'Email'],
            [VerifyStatus::UNCHECKED, 'Address'],
            [VerifyStatus::INVALID, 'Mobile'],
            [VerifyStatus::INVALID, 'Phone'],
        ];
    }
}
