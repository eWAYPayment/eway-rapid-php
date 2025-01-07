<?php

declare(strict_types=1);

namespace Eway\Test\Unit\Model\Support;

use Eway\Rapid\Model\Customer;
use PHPUnit\Framework\TestCase;

class CanGetClassTest extends TestCase
{
    /**
     * @return void
     */
    public function testGetClass(): void
    {
        $this->assertEquals(Customer::class, Customer::getClass());
    }
}
