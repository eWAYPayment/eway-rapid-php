<?php

namespace Eway\Rapid\Model\Response;

use Eway\Rapid\Model\ThreeDSecureAuth;

/**
 * @property string Errors
 * @property string AccessCode
 * @property bool Enrolled
 * @property ThreeDSecureAuth ThreeDSecureAuth
 */
class Verification3dsEnrolmentResponse extends AbstractResponse
{
    protected $fillable = [
        'Errors',
        'AccessCode',
        'Enrolled',
        'ThreeDSecureAuth',
    ];
}
