<?php

namespace Eway\Rapid\Model\Response;

use Eway\Rapid\Model\Customer;
use Eway\Rapid\Model\Support\HasCustomerTrait;
use Eway\Rapid\Model\Support\HasPaymentTrait;
use Eway\Rapid\Model\Support\HasVerificationTrait;

/**
 * The response is returned from a CreateTransaction method call.
 * It will echo back the details of the Customer that has been, or will be, created.
 * Additional fields may also be set when the Create request has a PaymentMethod
 * of Responsive Shared or Transparent Redirect.
 *
 * @property array    Errors           List of all validation, or processing, during the method call.
 *                                      empty/null if no errors occured. This member combines all
 *                                      errors related to the request.
 * @property Customer Customer         The Customer created by the method call. This will echo back
 *                                      the properties of the Customer adding the TokenCustomerID
 *                                      for the created customer.
 * @property string   SharedPaymentUrl (Only for payment method of ResponsiveShared)
 *                                      URL to the Responsive Shared Page that the cardholder's
 *                                      browser should be redirected to to capture the card to save
 *                                      with the new customer.
 * @property string   FormActionUrl    (Only for payment method of TransparentRedirect)
 *                                      URL That the merchant's credit card collection form should
 *                                      post to to capture the card to be saved with the new customer.
 * @property string   AccessCode       The AccessCode for this transaction (can be used with the
 *                                      customer query method call for searching before and after
 *                                      the card capture is completed)
 */
class CreateCustomerResponse extends AbstractResponse
{
    protected $fillable = [
        'SharedPaymentUrl',
        'FormActionUrl',
        'AccessCode',
        'AuthorisationCode',
        'BeagleScore',
        'Customer',
        'Errors',
        'Payment',
        'ResponseCode',
        'ResponseMessage',
        'TransactionID',
        'TransactionStatus',
        'TransactionType',
        'Verification',
        'FormActionURL',
        'CompleteCheckoutURL',
    ];

    use HasCustomerTrait, HasVerificationTrait, HasPaymentTrait;
}
