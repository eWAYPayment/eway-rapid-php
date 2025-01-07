<?php

declare(strict_types=1);

namespace Eway\Test\Unit\Model\Support;

use Eway\Rapid\Model\CardDetails;
use Eway\Rapid\Model\Customer;
use PHPUnit\Framework\TestCase;

class HasCardDetailTest extends TestCase
{
    /**
     * @return void
     */
    public function testSetCardDetailsAttribute(): void
    {
        $cardDetail = new CardDetails();
        $customer = new Customer();
        $customer->setCardDetailsAttribute($cardDetail);
        $this->assertEquals($cardDetail, $customer->CardDetails);
    }
}
