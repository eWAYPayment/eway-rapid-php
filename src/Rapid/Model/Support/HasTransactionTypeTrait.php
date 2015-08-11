<?php

namespace Eway\Rapid\Model\Support;

/**
 * Trait HasTransactionTypeTrait.
 */
trait HasTransactionTypeTrait
{
    /**
     * @param string $transactionType
     *
     * @return $this
     */
    public function setTransactionTypeAttribute($transactionType)
    {
        $this->validateEnum('Eway\Rapid\Enum\TransactionType', 'TransactionType', $transactionType);

        return $this;
    }
}
