<?php

namespace Eway\Rapid\Model\Response;

use Eway\Rapid\Model\Customer;
use Eway\Rapid\Model\Support\HasCustomersTrait;

/**
 * Class QueryCustomerResponse.
 *
 * @property Customer[] Customers
 * @property array      Errors
 */
class QueryCustomerResponse extends AbstractResponse
{
    use HasCustomersTrait;

    protected $fillable = [
        'Customers',
        'Errors',
    ];
}
