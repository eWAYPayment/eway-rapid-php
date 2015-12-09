<?php

namespace Eway\Rapid\Model;

/**
 * Class CardDetails.
 *
 * @property string $Name        Name on the card
 * @property string $Number      Credit card number (16-21 digits plaintext, Up to 512 chars for eCrypted values)
 * @property string $ExpiryMonth 2 Digits
 * @property string $ExpiryYear  2 or 4 digits e.g. "15" or "2015"
 * @property string $StartMonth  2 digits (required in some countries)
 * @property string $StartYear   2 or 4 digits (required in some countries)
 * @property string $IssueNumber Card issue number (required in some countries)
 * @property string $CVN         Required for transactions of type Purchase. Optional for other transaction types.
 *                                (3 or 4 digit number plaintext, up to 512 chars for eCrypted values)
 */
class CardDetails extends AbstractModel
{
    protected $fillable = [
        'Name',
        'Number',
        'ExpiryMonth',
        'ExpiryYear',
        'StartMonth',
        'StartYear',
        'IssueNumber',
        'CVN',
    ];
}
