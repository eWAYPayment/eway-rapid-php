<?php

namespace Eway\Rapid\Model\Response;

use Eway\Rapid\Model\Support\HasBeagleVerificationTrait;
use Eway\Rapid\Model\Support\HasVerificationTrait;
use Eway\Rapid\Model\Verification;

/**
 * Class QueryAccessCodeResponse.
 *
 * @property string       $AccessCode         An echo of the access code used in the request
 * @property string       $AuthorisationCode  The authorisation code for this transaction as returned by the bank
 * @property string       $BeagleScore        Fraud score representing the estimated probability that the order is fraud. This field will only be
 *           returned for transactions using the Beagle Free gateway.
 * @property Verification $BeagleVerification
 * @property string       $Errors             A comma separated list of any error encountered, these can be looked up in the Response Codes section.
 * @property string       $InvoiceNumber      An echo of the merchant’s invoice number for this transaction
 * @property string       $InvoiceReference   An echo of the merchant’s reference number for this transaction
 * @property array        $Options
 * @property string       $Message
 * @property string       $ResponseCode       The two digit response code returned from the bank
 * @property string       $ResponseMessage    One or more Response Codes that describes the result of the action performed. If a Beagle Alert is
 *           triggered, this may contain multiple codes: e.g. D4405, F7003
 * @property int          $TokenCustomerID    An eWAY-issued ID that represents the Token customer that was loaded or created for this transaction
 *           (if applicable)
 * @property int          $TotalAmount        The amount that was authorised for this transaction
 * @property int          $TransactionID      A unique identifier that represents the transaction in eWAY’s system
 * @property boolean      $TransactionStatus  A Boolean value that indicates whether the transaction was successful or not
 * @property Verification $Verification       A Boolean value that indicates whether the transaction was successful or not
 */
class QueryAccessCodeResponse extends AbstractResponse
{
    use HasBeagleVerificationTrait, HasVerificationTrait;

    protected $fillable = [
        'AccessCode',
        'AuthorisationCode',
        'BeagleScore',
        'BeagleVerification',
        'Errors',
        'InvoiceNumber',
        'InvoiceReference',
        'Message',
        'Options',
        'ResponseCode',
        'ResponseMessage',
        'TokenCustomerID',
        'TotalAmount',
        'TransactionID',
        'TransactionStatus',
        'Verification',
    ];
}
