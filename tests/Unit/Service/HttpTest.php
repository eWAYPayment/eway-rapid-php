<?php

declare(strict_types=1);

namespace Eway\Test\Unit\Service;

use Eway\Rapid\Service\Http;
use Eway\Rapid\Service\Http\Curl;
use Eway\Rapid\Service\Http\Response;
use InvalidArgumentException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class HttpTest extends TestCase
{
    /** @var Http $http */
    private $http;

    /** @var Curl|MockObject $curl */
    private $curl;

    /**
     * @return void
     */
    protected function setUp(): void
    {
        $this->curl = $this->createMock(Curl::class);
        $this->http = new Http('key', 'password', '/', $this->curl);
    }

    /**
     * @return void
     */
    public function testKey(): void
    {
        $key = 'key-01';
        $this->assertEquals($this->http, $this->http->setKey($key));
        $this->assertEquals($key, $this->http->getKey());
    }

    /**
     * @return void
     */
    public function testPassword(): void
    {
        $password = 'password-01';
        $this->assertEquals($this->http, $this->http->setPassword($password));
        $this->assertEquals($password, $this->http->getPassword());
    }

    /**
     * @return void
     */
    public function testBaseUrl(): void
    {
        $baseUrl = '/base';
        $this->assertEquals($this->http, $this->http->setBaseUrl($baseUrl));
        $this->assertEquals($baseUrl, $this->http->getBaseUrl());
    }

    /**
     * @return void
     */
    public function testVersion(): void
    {
        $version = 1.1;
        $this->assertEquals($this->http, $this->http->setVersion($version));
        $this->assertEquals($version, $this->http->getVersion());
    }

    /**
     * @dataProvider uriDataProvider
     * @param string $expected
     * @param $uri
     * @param $withBaseUrl
     * @return void
     */
    public function testUri(string $expected, $uri, $withBaseUrl): void
    {
        $this->http->setBaseUrl('https://example.com');
        $this->assertEquals($expected, $this->http->getUri($uri, $withBaseUrl));
    }

    /**
     * @return array[]
     */
    public function uriDataProvider(): array
    {
        return [
            ['/uri', '/uri', false],
            ['https://example.com/uri', '/uri', true],
            ['/uri/v/1', ['/uri/v/{ver}', ['ver' => 1]], false],
        ];
    }

    /**
     * @return void
     */
    public function testUriInvalid(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->http->getUri(['/uri/{path}', 'path=1'], false);
    }

    /**
     * @return void
     */
    public function testGetTransactionInvalid(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->http->getTransaction('');
    }

    /**
     * @dataProvider getRequestDataProvider
     * @param string $method
     * @param string|array $params
     * @return void
     */
    public function testGetRequest(string $method, $params): void
    {
        $this->createCurlMock();
        $this->assertEquals(new Response(), $this->http->{$method}($params));
    }

    /**
     * @return array[]
     */
    public function getRequestDataProvider(): array
    {
        return [
            ['getTransaction', 'reference'],
            ['getTransactionInvoiceNumber', 'invoice-number'],
            ['getTransactionInvoiceReference', 'invoice-reference'],
            ['getAccessCode', 'access-code'],
            ['getCustomer', 'token-customer-id'],
            ['getSettlementSearch', ['q' => 'search']],
        ];
    }

    /**
     * @dataProvider getRequestInvalidDataProvider
     * @param string $method
     * @return void
     */
    public function testGetRequestInvalid(string $method): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->http->{$method}('');
    }

    /**
     * @return array[]
     */
    public function getRequestInvalidDataProvider(): array
    {
        return [
            ['getTransaction'],
            ['getTransactionInvoiceNumber'],
            ['getTransactionInvoiceReference'],
            ['getAccessCode'],
            ['getCustomer'],
        ];
    }

    /**
     * @dataProvider postRequestDataProvider
     * @param string $method
     * @param array $params
     * @return void
     */
    public function testPostRequest(string $method, array $params): void
    {
        $this->createCurlMock();
        $this->assertEquals(
            new Response(),
            call_user_func_array([$this->http, $method], $params),
        );
    }

    /**
     * @return array[]
     */
    public function postRequestDataProvider(): array
    {
        return [
            ['postTransaction', ['data']],
            ['postTransactionRefund', ['transactionId', []]],
            ['postAccessCode', ['data']],
            ['postAccessCodeShared', ['data']],
            ['post3dsEnrolment', ['data']],
            ['post3dsEnrolmentVerification', ['data']],
            ['postCapturePayment', ['data']],
            ['postCancelAuthorisation', ['data']],
        ];
    }

    /**
     * @return void
     */
    public function testRequestWithError(): void
    {
        $this->curl->expects($this->once())->method('init');
        $this->curl->expects($this->once())->method('close');
        $this->curl->expects($this->once())->method('setOptions');
        $this->curl->expects($this->once())->method('getError')->willReturn('error');
        $this->curl->expects($this->once())->method('getErrorNo')->willReturn(CURLE_READ_ERROR);
        $this->curl->expects($this->exactly(2))->method('getInfo')->willReturnOnConsecutiveCalls(403, 4);

        $this->assertEquals(
            new Response(403, '', 'error'),
            $this->http->getAccessCode('access-code'),
        );
    }

    /**
     * @return void
     */
    private function createCurlMock(): void
    {
        $this->curl->expects($this->once())->method('init');
        $this->curl->expects($this->once())->method('close');
        $this->curl->expects($this->once())->method('setOptions');
        $this->curl->expects($this->once())->method('getVersion')
            ->willReturn(['version_number' => 468480]);
        $this->curl->expects($this->exactly(2))->method('getInfo')
            ->willReturnOnConsecutiveCalls(200, 43);
        $this->curl->expects($this->once())->method('getErrorNo')
            ->willReturn(0);
        $this->curl->expects($this->once())->method('execute')
            ->willReturn("HTTP/1.0 200 Connection established\r\n\r\nbody");
        $this->http->setVersion(1);
    }
}
