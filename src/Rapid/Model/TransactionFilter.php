<?php

namespace Eway\Rapid\Model;

/**
 * Class TransactionFilter.
 *
 * @property int    $TransactionID    The eWay transaction ID to search for.
 * @property string $AccessCode       The access code to search for.
 * @property string $InvoiceReference The Invoice reference to search for. Must be unique to return a transaction.
 * @property string $InvoiceNumber    The Invoice number to search for. Must be unique to return a transaction
 */
class TransactionFilter extends AbstractModel
{
    protected $fillable = [
        'TransactionID',
        'AccessCode',
        'InvoiceReference',
        'InvoiceNumber',
    ];
}
