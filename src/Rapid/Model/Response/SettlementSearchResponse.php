<?php

namespace Eway\Rapid\Model\Response;

/**
 * Class SettlementSearchResponse.
 *
 * @property array  SettlementSummaries
 * @property array  SettlementTransactions
 */
class SettlementSearchResponse extends AbstractResponse
{
    protected $fillable = [
        'SettlementSummaries',
        'SettlementTransactions',
        'Errors'
    ];
}
