<?php

declare(strict_types=1);

namespace Eway\Test\Unit\Model\Response;

use Eway\Rapid\Enum\TransactionType;
use Eway\Rapid\Model\Response\CreateTransactionResponse;
use Eway\Rapid\Model\Verification;
use PHPUnit\Framework\TestCase;

class CreateTransactionResponseTest extends TestCase
{
    /**
     * @dataProvider transactionTypeDataProvider
     * @param mixed $expected
     * @param mixed $value
     * @return void
     */
    public function testSetTransactionTypeAttribute($expected, $value): void
    {
        $transactionResponse = new CreateTransactionResponse();
        $transactionResponse->setTransactionTypeAttribute($value);
        $this->assertEquals($expected, $transactionResponse->TransactionType);
    }

    /**
     * @return array[]
     */
    public function transactionTypeDataProvider(): array
    {
        return [
            [null, 1],
            [null, 'Unknown'],
            [TransactionType::PURCHASE, TransactionType::PURCHASE],
        ];
    }

    /**
     * @return void
     */
    public function testSetVerificationAttribute(): void
    {
        $verification = new Verification();
        $transactionResponse = new CreateTransactionResponse();
        $transactionResponse->setVerificationAttribute($verification);
        $this->assertEquals($verification, $transactionResponse->Verification);
    }
}
