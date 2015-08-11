<?php


namespace Eway\Rapid\Model\Support;

/**
 * Trait HasBeagleVerificationTrait.
 */
trait HasBeagleVerificationTrait
{
    /**
     * @param $beagleVerification
     *
     * @return $this
     */
    public function setBeagleVerificationAttribute($beagleVerification)
    {
        $this->validateInstance('Eway\Rapid\Model\Verification', 'BeagleVerification', $beagleVerification);

        return $this;
    }
}
