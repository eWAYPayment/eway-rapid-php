<?php

namespace Eway\Rapid\Model\Support;

/**
 * Class HasCardDetailTrait.
 *
 * @property CardDetails $CardDetails
 */
trait HasCardDetailTrait
{
    /**
     * @param $cardDetails
     *
     * @return $this
     */
    public function setCardDetailsAttribute($cardDetails)
    {
        $this->validateInstance('Eway\Rapid\Model\CardDetails', 'CardDetails', $cardDetails);

        return $this;
    }
}
