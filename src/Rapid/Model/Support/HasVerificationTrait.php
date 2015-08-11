<?php

namespace Eway\Rapid\Model\Support;

/**
 * Trait HasVerificationTrait.
 */
trait HasVerificationTrait
{
    /**
     * @param mixed $verification
     *
     * @return $this
     */
    public function setVerificationAttribute($verification)
    {
        $this->validateInstance('Eway\Rapid\Model\Verification', 'Verification', $verification);

        return $this;
    }
}
