<?php

namespace Eway\Rapid\Model;

use Eway\Rapid\Model\Support\HasBeagleVerificationTrait;
use Eway\Rapid\Model\Support\HasCustomerTrait;
use Eway\Rapid\Model\Support\HasItemsTrait;
use Eway\Rapid\Model\Support\HasOptionsTrait;
use Eway\Rapid\Model\Support\HasPaymentTrait;
use Eway\Rapid\Model\Support\HasShippingAddressTrait;
use Eway\Rapid\Model\Support\HasTransactionTypeTrait;
use Eway\Rapid\Model\Support\HasVerificationTrait;

/**
 * Class Transaction.
 *
 * @property string          $TransactionType    What type of transaction this is (Purchase, MOTO,etc)
 * @property bool            $Capture            Set to true to create a regular transaction with immediate capture.
 *           Set to false to create an Authorisation transaction that can be used in a subsequent transaction.
 * @property bool            $SaveCustomer       Set to true to create a token for a customer when a transaction is
 *           complete.
 * @property Customer        $Customer           Customer details (name address token etc)
 * @property ShippingAddress $ShippingAddress    (optional) Shipping Address, name etc for the product ordered with
 *           this transaction
 * @property Payment         $Payment            Payment details (amount, currency and invoice information)
 * @property Item[]          $Items              (optional) Line Items for the purchase
 * @property array           $Options            (optional) General Options for the transaction
 * @property string          $DeviceID           (optional) Used to supply an identifier for the device sending the
 *           transaction.
 * @property string          $PartnerID          (optional) Used by shopping carts/ partners.
 * @property string          $ThirdPartyWalletID (optional) Deprecated - use $SecuredCardData
 * @property string          $SecuredCardData    (optional)Card data ID, used for Secure Fields, Visa Checkout, AMEX
 *           Express Checkout and Android Pay
 * @property int             $AuthTransactionID  (optional) Used with a PaymentType of Authorisation. This specifies
 *           the original authorisation that the funds are to be captured from.
 * @property string          $RedirectUrl        (optional) Used by transactions with a CardSource of
 *           TRANSPARENT_REDIRECT, or RESPONSIVE_SHARED. This field specifies the URL on the merchant's site that the
 *           RapidAPI will redirect the cardholder's browser to after processing the transaction.
 * @property string          $CancelUrl          (optional) Used by transactions with a card source of
 *           RESPONSIVE_SHARED. This field specifies the URL on the merchant's site that the responsive page redirect
 *           the cardholder to if they choose to cancel the transaction.
 * @property bool            $CheckoutPayment    (optional) Set to true if using PayPal Checkout
 * @property string          $CheckoutUrl        (optional) The URL used for PayPal Checkout to return to
 * @property string          $Method
 * @property string          $InvoiceNumber
 * @property string          $InvoiceDescription
 * @property string          $TokenCustomerID
 * @property string          $TransactionDateTime (v40+ query response only) The date and time the transaction took
 *                                                  place
 * @property boolean         $TransactionCaptured (v40+ query response only) True if funds were captured in the
 *                                                  transaction.
 * @property int             $Source              (v40+ query response only) Reserved for future use
 * @property int             $MaxRefund           (v40+ query response only) The maximum amount that could be refunded
 *                                                  from this transaction
 * @property int             $OriginalTransactionId (v40+ query response only) Contains the original transaction ID if
 *                                                  the queried transaction is a refund
 * @property string          $FraudAction         (v40+ query response only) The fraud action that occurred if any.
 *                                              One of NotChallenged, Allow, Review, PreAuth, Processed, Approved, Block
 * @property string          $CurrencyCode        (v40+ query response only) The ISO 4217 numeric currency code
 *                                                  (e.g. AUD = 036)
 *
 * @property array           $PaymentInstrument  (optional) Set to data for 3DS 2.0
 */
class Transaction extends AbstractModel
{
    use HasTransactionTypeTrait,
        HasCustomerTrait,
        HasShippingAddressTrait,
        HasPaymentTrait,
        HasItemsTrait,
        HasOptionsTrait,
        HasBeagleVerificationTrait,
        HasVerificationTrait;

    protected $fillable = [
        'TransactionType',
        'Capture',
        'SaveCustomer',
        'Customer',
        // Customer again
        'CustomerIP',
        'CustomerNote',
        'CustomerReadOnly',
        'CustomView',
        'TokenCustomerID',

        'ShippingAddress',
        'Payment',
        // Payment again
        'TotalAmount',
        'InvoiceNumber',
        'InvoiceDescription',
        'InvoiceReference',
        'CurrencyCode',

        'Items',
        'Options',
        'DeviceID',
        'PartnerID',
        'ThirdPartyWalletID',
        'SecuredCardData',
        'AuthTransactionID',
        'RedirectUrl',
        'CancelUrl',
        'CheckoutUrl',
        'CheckoutPayment',

        'HeaderText',
        'Language',
        'LogoUrl',

        'AuthorisationCode',
        'BeagleVerification',
        'Method',

        'TransactionStatus',
        // Transaction status again
        'BeagleScore',
        'Captured',
        'FraudAction',
        'Status',
        'Total',
        'TransactionID',

        'Verification',
        // Verification again
        'Address',
        'BeagleEmail',
        'BeaglePhone',
        'CVN',
        'Email',
        'Mobile',
        'Phone',

        'ProcessingDetails',
        // ProcessingDetails again
        'AuthorisationCode',
        'ResponseCode',
        'ResponseMessage',

        'VerifyCustomerEmail',
        'VerifyCustomerPhone',

        // v40 fields
        'TransactionDateTime',
        'TransactionCaptured',
        'Source',
        'MaxRefund',
        'OriginalTransactionId',

        // PaymentInstrument
        'PaymentInstrument'
    ];

    /**
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        if (!isset($attributes['Capture'])) {
            $attributes['Capture'] = true;
        }

        parent::__construct($attributes);
    }
}
