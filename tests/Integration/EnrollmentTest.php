<?php

declare(strict_types=1);

namespace Eway\Test\Integration;

use Eway\Rapid\Model\Response\Creation3dsEnrolmentResponse;
use Eway\Rapid\Model\Response\Verification3dsEnrolmentResponse;
use Eway\Rapid\Model\Transaction;

class EnrollmentTest extends AbstractTestCase
{
    /**
     * @return void
     */
    public function testCreate3dsEnrollment(): void
    {
        $this->setUpCurl(200, ['AccessCode' => '1234', 'Default3dsUrl' => 'https://example.com/3ds']);
        $response = $this->client->create3dsEnrolment(new Transaction());
        $this->assertInstanceOf(Creation3dsEnrolmentResponse::class, $response);
        $this->assertIsArray($response->getErrors());
        $this->assertEmpty($response->getErrors());
        $this->assertEquals('1234', $response->AccessCode);
        $this->assertEquals('https://example.com/3ds', $response->Default3dsUrl);
    }

    /**
     * @return void
     */
    public function testVerify3dsEnrollment(): void
    {
        $responseData = [
            'AccessCode' => '1234',
            'Enrolled' => true,
            'ThreeDSecureAuth' => [
                'Cryptogram' => '2FVngpfh1sZWuSkUWedPTUfbRZVe',
                'ECI' => '05',
                'XID' => '2FVngpfh1sZWuSkUWedPTUfbRZVe',
                'AuthStatus' => 'Y',
                'Version' => '2.1.0',
                'dsTransactionId' => '34898df8-aeaf-4cc1-9200-b18c88b52522',
            ],
        ];

        $this->setUpCurl(200, $responseData);
        $response = $this->client->verify3dsEnrolment(new Transaction());
        $this->assertInstanceOf(Verification3dsEnrolmentResponse::class, $response);
        $this->assertIsArray($response->getErrors());
        $this->assertEmpty($response->getErrors());
        $this->assertEquals('1234', $response->AccessCode);
        $this->assertTrue($response->Enrolled);
        $this->assertEquals($responseData['ThreeDSecureAuth'], $response->ThreeDSecureAuth);
    }
}
