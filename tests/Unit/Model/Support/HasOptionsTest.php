<?php

declare(strict_types=1);

namespace Eway\Test\Unit\Model\Support;

use Eway\Rapid\Model\Transaction;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class HasOptionsTest extends TestCase
{
    /**
     * @return void
     */
    public function testSetOptionsAttribute(): void
    {
        $options = ['foo' => 'bar'];
        $transaction = new Transaction();
        $transaction->setOptionsAttribute($options);
        $this->assertEquals($options, $transaction->Options);
    }

    /**
     * @return void
     */
    public function testSetOptionsAttributeInvalid(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $transaction = new Transaction();
        $transaction->setOptionsAttribute('');
    }
}
