<?php

declare(strict_types=1);

namespace Eway\Test\Integration;

use Eway\Rapid\Enum\ApiMethod;
use Eway\Rapid\Enum\VerifyStatus;
use Eway\Rapid\Model\Item;
use Eway\Rapid\Model\Response\QueryCustomerResponse;
use Eway\Rapid\Model\Response\QueryTransactionResponse;
use Eway\Rapid\Model\Transaction;
use Eway\Rapid\Validator\EnumValidator;
use InvalidArgumentException;
use stdClass;

class ModelTest extends AbstractTestCase
{
    /**
     * @return void
     */
    public function testItem(): void
    {
        $item = new Item();
        $item->calculate(5, 0.5, 2);
        $this->assertEquals(1, $item->Tax);
        $this->assertEquals(11, $item->Total);
    }

    public function testHasPaymentTrait(): void
    {
        $transaction = new Transaction(['Payment' => null]);
        $this->assertNull($transaction->Payment);
    }

    /**
     * @return void
     */
    public function testHasCustomersTrait(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new QueryCustomerResponse(['Customers' => '']);
    }

    /**
     * @dataProvider traitDataProvider
     * @param array $data
     * @return void
     */
    public function testTraitsInTransaction(array $data): void
    {
        $this->expectException(InvalidArgumentException::class);
        new Transaction($data);
    }

    public function traitDataProvider(): array
    {
        return [
            [['Items' => '']],
            [['Options' => '']],
        ];
    }

    /**
     * @return void
     */
    public function testHasTransactionsTrait(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new QueryTransactionResponse(['Transactions' => '']);
    }

    /**
     * @return void
     */
    public function testUnset(): void
    {
        $item = new Item(['SKU' => 'sku']);
        unset($item->SKU);
        $this->assertNull($item->SKU);
    }

    /**
     * @dataProvider enumValidatorDataProvider
     * @param string $class
     * @return void
     */
    public function testEnumValidator(string $class): void
    {
        $this->expectException(InvalidArgumentException::class);
        EnumValidator::validate($class, 'ApiMethod', 'value');
    }

    /**
     * @return array
     */
    public function enumValidatorDataProvider(): array
    {
        return [
            [stdClass::class],
            [ApiMethod::class],
        ];
    }

    /**
     * @return void
     */
    public function testGetOptionsArray(): void
    {
        $options = VerifyStatus::getOptionsArray();
        $this->assertIsArray($options);
        $this->assertEquals(
            ['UNCHECKED' => 0, 'VALID' => 1, 'INVALID' => 2],
            $options
        );
    }
}
