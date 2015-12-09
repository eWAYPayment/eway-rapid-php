<?php

namespace Eway\Rapid\Model;

/**
 * Class Refund.
 *
 * @property int    $TransactionID      The ID of either the transaction to refund, or the authorisation to cancel.
 * @property int    $TotalAmount        The total amount to refund the card holder in this transaction in cents.
 *                                      e.g. 1000 = $10.00
 * @property string $InvoiceNumber      The merchant's invoice number
 * @property string $InvoiceDescription merchants invoice description
 * @property string $InvoiceReference   The merchant's invoice reference
 * @property string $CurrencyCode       The merchant's currency (e.g. AUD)
 */
class RefundDetails extends AbstractModel
{
    protected $fillable = [
        'TransactionID',
        'TotalAmount',
        'InvoiceNumber',
        'InvoiceDescription',
        'InvoiceReference',
        'CurrencyCode',
    ];
}
