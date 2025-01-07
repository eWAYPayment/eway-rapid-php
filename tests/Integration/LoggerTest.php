<?php

declare(strict_types=1);

namespace Eway\Test\Integration;

use Eway\Rapid\Enum\LogLevel;
use Eway\Rapid\Model\Item;
use Eway\Rapid\Service\Logger;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class LoggerTest extends TestCase
{
    /**
     * @dataProvider logDataProvider
     * @param string $level
     * @return void
     */
    public function testLog(string $level): void
    {
        $item = $this->createMock(Item::class);
        $item->expects($this->once())->method('__toString')->willReturn('[item]');

        $logger = new Logger();
        $logger->{$level}('message', [
            'bool' => true,
            'null' => null,
            'array' => ['a', 'b'],
            'item' => $item,
            'resource' => fopen('php://temp', 'r+'),
        ]);
    }

    /**
     * @return array[]
     */
    public function logDataProvider(): array
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
    public function testInvalidLevel(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $logger = new Logger();
        $logger->log('LEVEL', 'message');
    }
}









