<?php

declare(strict_types=1);

namespace Eway\Test\Integration;

use Eway\Rapid;
use Eway\Rapid\Client;
use Eway\Rapid\Contract\Client as ClientContract;
use Eway\Rapid\Service\Http;
use Eway\Rapid\Service\Http\Curl;
use Eway\Rapid\Service\Logger;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

abstract class AbstractTestCase extends TestCase
{
    /** @var Client $client */
    protected $client;

    /** @var Logger|MockObject $logger */
    protected $logger;

    /** @var Curl|MockObject $curl */
    protected $curl;

    /**
     * @return void
     */
    protected function setUp(): void
    {
        $this->curl = $this->createMock(Curl::class);
        $this->logger = $this->createMock(Logger::class);
        $this->client = Rapid::createClient('api-key', 'password', ClientContract::MODE_SANDBOX, $this->logger);
        $this->client->setHttpService(new Http('api-key', 'password', 'https://example.com', $this->curl));
        $this->client->setVersion(1);
    }

    /**
     * @param int $status
     * @param mixed $body
     * @param string|null $error
     * @param int|null $errorNo
     * @return void
     */
    protected function setUpCurl(int $status, $body, string $error = '', int $errorNo = 0): void
    {
        $this->curl->expects($this->once())
            ->method('execute')
            ->willReturn(is_array($body) ? json_encode($body) : $body);
        $this->curl->expects($this->exactly(2))->method('getInfo')->willReturnOnConsecutiveCalls($status, 0);
        $this->curl->expects($this->once())->method('getErrorNo')->willReturn($errorNo);
        $this->curl->expects($this->any())->method('getError')->willReturn($error);
    }
}
