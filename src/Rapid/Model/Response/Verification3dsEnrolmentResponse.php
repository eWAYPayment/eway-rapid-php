<?php

namespace Eway\Rapid\Model\Response;

use Eway\Rapid\Model\ThreeDSecureAuth;

/**
 * @property string Errors
 * @property string AccessCode
 * @property bool Enrolled
 * @property ThreeDSecureAuth ThreeDSecureAuth
 */
class Verification3DsEnrolmentResponse extends AbstractResponse
{
    protected $fillable = [
        'Errors',
        'AccessCode',
        'Enrolled',
        'ThreeDSecureAuth',
    ];
}
