<?php

declare(strict_types=1);

namespace Eway\Test\Unit\Model;

use Eway\Rapid\Model\Address;
use PHPUnit\Framework\TestCase;

class AddressTest extends TestCase
{
    /** @var Address $address */
    private $address;

    /** @var string[] $data */
    private $data = [
        'Street1' => 'street 1',
        'Street2' => 'street 2',
        'City' => 'city',
        'State' => 'state',
        'Country' => 'country',
        'PostalCode' => '1234',
    ];

    /**
     * @return void
     */
    protected function setUp(): void
    {
        $this->address = new Address($this->data);
    }

    /**
     * @return void
     */
    public function testToJsonAndToString(): void
    {
        $json = json_encode($this->data);
        $this->assertEquals($json, $this->address->toJson());
        $this->assertEquals($json, $this->address->__toString());
    }

    /**
     * @return void
     */
    public function testToArray(): void
    {
        $this->assertEquals($this->data, $this->address->toArray());
    }
}
