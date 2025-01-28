<?php

declare(strict_types=1);

namespace Eway\Test\Unit\Model\Response;

use Eway\Rapid\Model\Response\QueryTransactionResponse;
use Eway\Rapid\Model\Transaction;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class QueryTransactionResponseTest extends TestCase
{
    /**
     * @dataProvider getErrorsDataProvider
     * @param array $expected
     * @param array $data
     * @return void
     */
    public function testErrors(array $expected, array $data): void
    {
        $transactionResponse = new QueryTransactionResponse($data);
        $this->assertEquals($expected, $transactionResponse->getErrors());
    }

    /**
     * @return array[]
     */
    public function getErrorsDataProvider(): array
    {
        return [
            [['A', 'B'], ['Errors' => 'A,B']],
            [[], []],
        ];
    }

    /**
     * @return void
     */
    public function testAddError(): void
    {
        $transactionResponse = new QueryTransactionResponse();
        $transactionResponse->addError('E');
        $this->assertEquals(['E'], $transactionResponse->getErrors());
    }

    /**
     * @return void
     */
    public function testSetTransactionsAttributes(): void
    {
        $transactionResponse = new QueryTransactionResponse();
        $transactionResponse->setTransactionsAttribute([new Transaction(), []]);
        $this->assertEquals([new Transaction(), new Transaction()], $transactionResponse->Transactions);
    }

    /**
     * @return void
     */
    public function testSetTransactionsAttributesInvalid(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $transactionResponse = new QueryTransactionResponse();
        $transactionResponse->setTransactionsAttribute(null);
    }
}
