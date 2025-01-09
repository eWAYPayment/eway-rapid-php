<?php

declare(strict_types=1);

namespace Eway\Test\Unit\Model\Support;

use Eway\Rapid\Model\Transaction;
use Eway\Rapid\Model\Verification;
use PHPUnit\Framework\TestCase;

class HasBeagleVerificationTest extends TestCase
{
    /**
     * @return void
     */
    public function testSetBeagleVerificationAttribute(): void
    {
        $verification = new Verification();
        $transaction = new Transaction();
        $transaction->setBeagleVerificationAttribute($verification);
        $this->assertEquals($verification, $transaction->getAttribute('BeagleVerification'));
    }
}
