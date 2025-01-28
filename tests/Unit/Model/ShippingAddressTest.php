<?php

declare(strict_types=1);

namespace Eway\Test\Unit\Model;

use Eway\Rapid\Enum\ShippingMethod;
use Eway\Rapid\Model\ShippingAddress;
use PHPUnit\Framework\TestCase;

class ShippingAddressTest extends TestCase
{
    /**
     * @dataProvider shippingMethodDataProvider
     * @param string $expected
     * @param string|null $method
     * @param array $data
     * @return void
     */
    public function testShippingMethod(string $expected, ?string $method, array $data): void
    {
        $shippingAddress = new ShippingAddress($data);
        $shippingAddress->setShippingMethodAttribute($method);
        $this->assertEquals($expected, $shippingAddress->ShippingMethod);
    }

    /**
     * @return array[]
     */
    public function shippingMethodDataProvider(): array
    {
        return [
            [ShippingMethod::UNKNOWN, null, []],
            [ShippingMethod::LOW_COST, ShippingMethod::LOW_COST, []],
        ];
    }
}
