<?php

declare(strict_types=1);

namespace Eway\Test\Unit\Model\Support;

use Eway\Rapid\Model\Item;
use Eway\Rapid\Model\Refund;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class HasItemsTest extends TestCase
{
    /**
     * @return void
     */
    public function testItemsAttribute(): void
    {
        $refund = new Refund();
        $refund->setItemsAttribute([new Item(), []]);
        $this->assertEquals([new Item(), new Item()], $refund->Items);
    }

    /**
     * @return void
     */
    public function testItemsAttributeInvalid(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $refund = new Refund();
        $refund->setItemsAttribute('');
    }
}
