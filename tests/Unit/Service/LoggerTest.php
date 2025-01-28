<?php

declare(strict_types=1);

namespace Eway\Test\Unit\Service;

use Eway\Rapid\Enum\LogLevel;
use Eway\Rapid\Model\Item;
use Eway\Rapid\Service\Logger;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class LoggerTest extends TestCase
{
    /**
     * @return void
     */
    public function testLogLevel(): void
    {
        $this->assertEquals(
            [
                'EMERGENCY' => LogLevel::EMERGENCY,
                'ALERT' => LogLevel::ALERT,
                'CRITICAL' => LogLevel::CRITICAL,
                'ERROR' => LogLevel::ERROR,
                'WARNING' => LogLevel::WARNING,
                'NOTICE' => LogLevel::NOTICE,
                'INFO' => LogLevel::INFO,
                'DEBUG' => LogLevel::DEBUG,
            ],
            LogLevel::getOptionsArray(),
        );
    }

    /**
     * @dataProvider methodDataProvider
     * @param string $level
     * @return void
     */
    public function testLog(string $level): void
    {
        $item = $this->createMock(Item::class);
        $item->expects($this->once())->method('__toString')->willReturn('item');

        $logger = new Logger();
        $logger->{$level}('message', [
            'null' => null,
            'object' => $item,
            'boolean' => true,
            'string' => 'string',
            'array' => ['a', 'b'],
            'resource' => fopen('php://temp', 'r+'),
        ]);
    }

    /**
     * @return array[]
     */
    public function methodDataProvider(): array
    {
        return [
            [LogLevel::EMERGENCY],
            [LogLevel::ALERT],
            [LogLevel::CRITICAL],
            [LogLevel::ERROR],
            [LogLevel::WARNING],
            [LogLevel::NOTICE],
            [LogLevel::INFO],
            [LogLevel::DEBUG],
        ];
    }

    /**
     * @return void
     */
    public function testLogLevelInvalid(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $logger = new Logger();
        $logger->log('LEVEL', 'message');
    }
}
