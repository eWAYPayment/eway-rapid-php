<?php

declare(strict_types=1);

namespace Eway\Test\Integration;

use Eway\Rapid;
use Eway\Rapid\Contract\Client;

class ClientTest extends AbstractTestCase
{
    /**
     * @dataProvider modeProvider
     * @param string $mode
     * @param string $endpoint
     * @param bool $withBaseUrl
     * @param string $uri
     * @return void
     */
    public function testCreateClient(string $mode, string $endpoint, bool $withBaseUrl, string $uri): void
    {
        $client = Rapid::createClient('key', 'password', $mode);
        $client->setVersion(1);

        $this->assertEquals($endpoint, $client->getEndpoint());
        $this->assertEquals(1, $client->getHttpService()->getVersion());
        $this->assertEquals('key', $client->getHttpService()->getKey());
        $this->assertEquals('password', $client->getHttpService()->getPassword());
        $this->assertEquals($endpoint, $client->getHttpService()->getBaseUrl());
        $this->assertEquals($uri, $client->getHttpService()->getUri('/path', $withBaseUrl));
    }

    /**
     * @return array[]
     */
    public function modeProvider(): array
    {
        return [
            [Client::MODE_SANDBOX, Client::ENDPOINT_SANDBOX, true, Client::ENDPOINT_SANDBOX . '/path'],
            [Client::MODE_PRODUCTION, Client::ENDPOINT_PRODUCTION, false, '/path'],
        ];
    }

    /**
     * @dataProvider endpointDataProvider
     * @param string $endpoint
     * @param bool $valid
     * @return void
     */
    public function testEndpointValidation(string $endpoint, bool $valid): void
    {
        $this->client->setEndpoint($endpoint);
        $this->assertEquals($valid, $this->client->isValid());
    }

    /**
     * @return array[]
     */
    public function endpointDataProvider(): array
    {
        return [
            ['https://example.com/', true],
            ['endpoint', false],
        ];
    }

    /**
     * @dataProvider messageDataProvider
     * @param string $expected
     * @param string $errorCode
     * @return void
     */
    public function testGetMessage(string $expected, string $errorCode, string $language): void
    {
        $this->assertEquals($expected, Rapid::getMessage($errorCode, $language));
    }

    /**
     * @return array[]
     */
    public function messageDataProvider(): array
    {
        return [
            ['Error', 'D4406', 'en'],
            ['not-existing', 'not-existing', 'en'],
            ['fr-error-code', 'fr-error-code', 'fr'],
        ];
    }
}
