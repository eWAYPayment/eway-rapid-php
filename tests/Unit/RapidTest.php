<?php

declare(strict_types=1);

namespace Eway\Test\Unit;

use Eway\Rapid;
use Eway\Rapid\Client;
use PHPUnit\Framework\TestCase;

class RapidTest extends TestCase
{
    /**
     * @return void
     */
    public function testCreateClient(): void
    {
        $this->assertInstanceOf(Client::class, Rapid::createClient('api-key', 'password'));
    }

    /**
     * @dataProvider getMessageDataProvider
     * @param string $errorCode
     * @param string $errorMessage
     * @param string $language
     * @return void
     */
    public function testGetMessage(string $errorCode, string $errorMessage, string $language): void
    {
        $this->assertSame($errorMessage, Rapid::getMessage($errorCode, $language));
    }

    /**
     * @return array[]
     */
    public function getMessageDataProvider(): array
    {
        return [
            ['3D99', 'System Error', 'en'],
            ['ERROR-CODE', 'ERROR-CODE', 'en'],
            ['Error', 'Error', 'fr'],
        ];
    }
}
