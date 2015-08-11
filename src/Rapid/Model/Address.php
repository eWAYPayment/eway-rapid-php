<?php

namespace Eway\Rapid\Model;

/**
 * Class Address.
 *
 * @property string $Street1    First line of the street address. e.g. "Unit 1"
 * @property string $Street2    Second line of the street address. e.g. "6 Coonabmble st"
 * @property string $City       City for the address, e.g. "Gulargambone"
 * @property string $State      State or province code. e.g. 'NSW"
 * @property string $Country    Two digit Country Code. e.g. "AU"
 * @property string $PostalCode e.g. 2828
 */
class Address extends AbstractModel
{
    protected $fillable = [
        'Street1',
        'Street2',
        'City',
        'State',
        'Country',
        'PostalCode',
    ];
}
