<?php

namespace Eway\Rapid\Model;

/**
 * @property string Cryptogram
 * @property string ECI
 * @property string XID
 * @property string AuthStatus
 * @property string Version
 * @property string dsTransactionId
 */
class ThreeDSecureAuth extends AbstractModel
{
    protected $fillable = [
        'Cryptogram',
        'ECI',
        'XID',
        'AuthStatus',
        'Version',
        'dsTransactionId',
    ];
}
