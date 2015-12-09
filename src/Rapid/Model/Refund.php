<?php

namespace Eway\Rapid\Model;

use Eway\Rapid\Model\Support\HasCustomerTrait;
use Eway\Rapid\Model\Support\HasItemsTrait;
use Eway\Rapid\Model\Support\HasRefundTrait;
use Eway\Rapid\Model\Support\HasShippingAddressTrait;

/**
 * Class Refund.
 *
 * @property string          $TransactionID   The ID of the transaction to refund.
 * @property Customer        $Customer        This set of fields contains the details of the merchant's customer.
 * @property ShippingAddress $ShippingAddress The ShippingAddress section is optional. It is used by Beagle
 *                                            Fraud Alerts to calculate a  risk score for this transaction.
 * @property RefundDetails   $Refund          This set of fields contains the details of the refund being processed.
 * @property Item[]          $Items           The Items section is optional. If provided, it should contain an array
 *                                            of Items purchased by the customer, up to a maximum of 99 items.
 * @property array           $Options         This section is optional. Anything appearing in this section is not
 *                                            displayed to the customer, but it is returned to the merchant in
 *                                            the result. Up to 99 options can be defined.
 * @property string          $DeviceID        The identification name/number for the device or application used to
 *                                            process the transaction
 * @property string          $PartnerID       Used by shopping carts/partners.
 */
class Refund extends AbstractModel
{
    use HasCustomerTrait, HasShippingAddressTrait, HasItemsTrait, HasRefundTrait;

    protected $fillable = [
        'TransactionID',
        'Customer',
        'ShippingAddress',
        'Refund',
        'Items',
        'Options',
        'DeviceID',
        'PartnerID',
        'CustomerIP',
    ];
}
