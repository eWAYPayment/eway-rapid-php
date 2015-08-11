<?php

namespace Eway\Rapid\Model\Support;

/**
 * Trait HasShippingAddressTrait.
 */
trait HasShippingAddressTrait
{
    /**
     * @param mixed $shippingDetails
     *
     * @return $this
     */
    public function setShippingAddressAttribute($shippingDetails)
    {
        $this->validateInstance('Eway\Rapid\Model\ShippingAddress', 'ShippingAddress', $shippingDetails);

        return $this;
    }
}
