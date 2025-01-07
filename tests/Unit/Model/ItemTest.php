<?php

declare(strict_types=1);

namespace Eway\Test\Unit\Model;

use Eway\Rapid\Model\Item;
use PHPUnit\Framework\TestCase;

class ItemTest extends TestCase
{
    /**
     * @return void
     */
    public function testCalculate(): void
    {
        $item = new Item();
        $item->calculate(32, 1.5, 2);

        $this->assertEquals(3, $item->Tax);
        $this->assertEquals(67, $item->Total);
    }

    /**
     * @dataProvider calculateTotalDataProvider
     * @param mixed $expected
     * @param array $data
     * @return void
     */
    public function testCalculateTotal($expected, array $data): void
    {
        $item = new Item($data);
        $this->assertEquals($expected, $item->Total);
    }

    /**
     * @return array[]
     */
    public function calculateTotalDataProvider(): array
    {
        return [
            [105, ['Quantity' => 2, 'UnitCost' => 50, 'Tax' => 5]],
            [175, ['Quantity' => 5, 'UnitCost' => 35]],
        ];
    }
}
