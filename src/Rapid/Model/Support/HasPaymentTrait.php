<?php

namespace Eway\Rapid\Model\Support;

/**
 * Trait HasPaymentTrait.
 *
 * @property Payment $Payment Payment details (amount, currency and invoice information)
 */
trait HasPaymentTrait
{
    /**
     * @param mixed $payment
     *
     * @return $this
     */
    public function setPaymentAttribute($payment)
    {
        $this->validateInstance('Eway\Rapid\Model\Payment', 'Payment', $payment);

        return $this;
    }
}
