<?php

namespace Eway\Rapid\Model\Response;

use Eway\Rapid\Model\Support\HasTransactionsTrait;
use Eway\Rapid\Model\Transaction;

/**
 * This response simply wraps the TransactionStatus type with the additional common fields required by a return type.
 *
 * @property string        $Errors       A comma separated list of any error encountered,
 *                                      these can be looked up using Rapid::getMessage().
 * @property Transaction[] $Transactions All transactions found
 */
class QueryTransactionResponse extends AbstractResponse
{
    use HasTransactionsTrait;

    protected $fillable = [
        'Transactions',
        'Errors',
        'Message',
    ];
}
