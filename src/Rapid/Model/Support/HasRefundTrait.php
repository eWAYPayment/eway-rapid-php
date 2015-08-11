<?php

namespace Eway\Rapid\Model\Support;

/**
 * Trait HasRefundTrait.
 */
trait HasRefundTrait
{
    /**
     * @param mixed $refund
     *
     * @return $this
     */
    public function setRefundAttribute($refund)
    {
        $this->validateInstance('Eway\Rapid\Model\RefundDetails', 'Refund', $refund);

        return $this;
    }
}
