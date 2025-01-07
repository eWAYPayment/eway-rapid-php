<?php

declare(strict_types=1);

namespace Eway\Test\Unit\Model;

use Eway\Rapid\Model\Refund;
use Eway\Rapid\Model\RefundDetails;
use Eway\Rapid\Model\ShippingAddress;
use PHPUnit\Framework\TestCase;

class RefundTest extends TestCase
{
    /**
     * @return void
     */
    public function testSetRefundAttribute(): void
    {
        $refundDetails = new RefundDetails();
        $refund = new Refund();

        $refund->setRefundAttribute($refundDetails);
        $this->assertEquals($refundDetails, $refund->Refund);
    }

    /**
     * @return void
     */
    public function testSetShippingAddressAttribute(): void
    {
        $shippingAddress = new ShippingAddress();
        $refund = new Refund();

        $refund->setShippingAddressAttribute($shippingAddress);
        $this->assertEquals($shippingAddress, $refund->ShippingAddress);
    }
}
