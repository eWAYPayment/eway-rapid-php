<?php

namespace Eway\Rapid\Model\Response;

use Eway\Rapid\Model\Customer;
use Eway\Rapid\Model\Refund;
use Eway\Rapid\Model\Support\HasCustomerTrait;
use Eway\Rapid\Model\Support\HasRefundTrait;
use Eway\Rapid\Model\Support\HasVerificationTrait;
use Eway\Rapid\Model\Verification;

/**
 * This Response is returned by the Refund Method. It wraps the TransactionStatus and
 * the Echoed back Refund Type with the standard error fields required by an API return type.
 *
 * @property string       AuthorisationCode
 * @property Customer     Customer
 * @property array        Errors
 * @property Refund       Refund
 * @property string       ResponseCode
 * @property string       ResponseMessage
 * @property string       TransactionID
 * @property string       TransactionStatus
 * @property Verification Verification
 */
class RefundResponse extends AbstractResponse
{
    use HasCustomerTrait, HasVerificationTrait, HasRefundTrait;

    protected $fillable = [
        'AuthorisationCode',
        'Customer',
        'Errors',
        'Refund',
        'ResponseCode',
        'ResponseMessage',
        'TransactionID',
        'TransactionStatus',
        'Verification',
    ];
}
