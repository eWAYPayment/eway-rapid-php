<?php

declare(strict_types=1);

namespace Eway\Test\Unit\Service\Http;

use Eway\Rapid\Service\Http\Response;
use PHPUnit\Framework\TestCase;

class ResponseTest extends TestCase
{
    /**
     * @dataProvider getDataProvider
     * @param int $status
     * @param string|null $body
     * @param string|null $error
     * @return void
     */
    public function testGet(int $status, ?string $body, ?string $error): void
    {
        $response = new Response($status, $body, $error);
        $this->assertEquals($status, $response->getStatusCode());
        $this->assertEquals($body, $response->getBody());
        $this->assertEquals($error, $response->getError());
    }

    /**
     * @return array[]
     */
    public function getDataProvider(): array
    {
        return [
            [200, 'body', null],
            [404, null, 'not-found'],
            [403, '', 'forbidden'],
            [999, null, null],
        ];
    }
}
