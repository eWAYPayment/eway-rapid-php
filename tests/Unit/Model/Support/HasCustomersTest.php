<?php

declare(strict_types=1);

namespace Eway\Test\Unit\Model\Support;

use Eway\Rapid\Model\Customer;
use Eway\Rapid\Model\Response\QueryCustomerResponse;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class HasCustomersTest extends TestCase
{
    /**
     * @return void
     */
    public function testSetCustomerAttribute(): void
    {
        $queryCustomer = new QueryCustomerResponse();
        $queryCustomer->setCustomersAttribute([new Customer(), []]);
        $this->assertEquals(
            [new Customer(), new Customer()],
            $queryCustomer->Customers
        );
    }

    /**
     * @return void
     */
    public function testSetCustomerAttributeInvalid(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $queryCustomer = new QueryCustomerResponse();
        $queryCustomer->setCustomersAttribute(null);
    }
}
