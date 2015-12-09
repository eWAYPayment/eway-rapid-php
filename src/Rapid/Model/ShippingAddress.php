<?php

namespace Eway\Rapid\Model;

use Eway\Rapid\Enum\ShippingMethod;

/**
 * Class ShippingAddress.
 *
 * @property string $City            The customer's shipping city / town / suburb.
 * @property string $Country         The customer's shipping country. This should be the
 *                                    two letter ISO 3166-1 alpha-2 code. This field must be lower
 *                                    case. e.g. Australia = au
 * @property string $Email           The customer's shipping email address
 * @property string $Fax             The fax number of the shipping location.
 * @property string $FirstName       The first name of the person the order is shipped to.
 * @property string $LastName        The last name of the person the order is shipped to.
 * @property string $Phone           The phone number of the person the order is shipped to.
 * @property string $PostalCode      The customer's shipping post / zip code.
 * @property string $ShippingMethod  ShippingMethod enum.
 * @property string $State           The customer's shipping state / county.
 * @property string $Street1         The street address the order is shipped to.
 * @property string $Street2         The street address of the shipping location.
 */
class ShippingAddress extends AbstractModel
{
    protected $fillable = [
        'FirstName',
        'LastName',
        'ShippingMethod',
        'Street1',
        'Street2',
        'City',
        'State',
        'Country',
        'PostalCode',
        'Email',
        'Phone',
        'Fax',
    ];

    /**
     * @param string $shippingMethod
     *
     * @return $this
     */
    public function setShippingMethodAttribute($shippingMethod)
    {
        if (null === $shippingMethod) {
            $this->attributes['ShippingMethod'] = ShippingMethod::UNKNOWN;
        } else {
            $this->validateEnum('Eway\Rapid\Enum\ShippingMethod', 'ShippingMethod', $shippingMethod);
        }

        return $this;
    }
}
