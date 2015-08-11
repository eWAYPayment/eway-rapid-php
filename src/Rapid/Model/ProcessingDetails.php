<?php

namespace Eway\Rapid\Model;

/**
 * Class ProcessingDetails.
 *
 * @property string $AuthorisationCode The Bank Auth code for the transaction
 * @property string $ResponseCode      The bank/gateway Response code
 * @property string $ResponseMessage   The bank/gateway response message
 */
class ProcessingDetails extends AbstractModel
{
    protected $fillable = [
        'AuthorisationCode',
        'ResponseCode',
        'ResponseMessage',
    ];
}
