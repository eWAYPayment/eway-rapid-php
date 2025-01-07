<?php

declare(strict_types=1);

namespace Eway\Test\Integration;

use Eway\Rapid\Contract\Client;
use Eway\Rapid\Exception\MassAssignmentException;
use Eway\Rapid\Model\Response\AbstractResponse;
use Eway\Rapid\Model\Response\QueryTransactionResponse;
use Eway\Rapid\Service\Http;
use Eway\Rapid\Service\Http\Response;
use InvalidArgumentException;
use Throwable;

class ErrorTest extends AbstractTestCase
{
    /**
     * @dataProvider invokeExceptionDataProvider
     * @param Throwable $exception
     * @param string $errorCode
     * @return void
     */
    public function testInvokeHasExceptions(Throwable $exception, string $errorCode): void
    {
        $this->curl->expects($this->once())->method('execute')->willThrowException($exception);
        $response = $this->client->queryTransaction('reference');
        $this->assertInstanceOf(QueryTransactionResponse::class, $response);
        $this->assertIsArray($response->getErrors());
        $this->assertNotEmpty($response->getErrors());
        $this->assertContains($errorCode, $response->getErrors());
    }

    /**
     * @return array[]
     */
    public function invokeExceptionDataProvider(): array
    {
        return [
            [new InvalidArgumentException(), Client::ERROR_INVALID_ARGUMENT],
            [new MassAssignmentException(), Client::ERROR_INVALID_ARGUMENT],
        ];
    }

    /**
     * @return void
     */
    public function testInvokeInvalidCredentials(): void
    {
        $this->client->setCredential('', '');
        $response = $this->client->queryTransaction('reference');
        $this->assertInstanceOf(QueryTransactionResponse::class, $response);
        $this->assertIsArray($response->getErrors());
        $this->assertNotEmpty($response->getErrors());
        $this->assertContains(Client::ERROR_INVALID_CREDENTIAL, $response->getErrors());
    }

    /**
     * @dataProvider invalidResponseDataProvider
     * @param Response|null $response
     * @param string $errorCode
     * @return void
     */
    public function testInvokeReturnError(?Response $response, string $errorCode): void
    {
        $http = $this->createMock(Http::class);
        $http->expects($this->once())->method('getTransaction')->willReturn($response);
        $this->logger->expects($this->once())->method('error');
        $this->client->setHttpService($http);

        $response = $this->client->queryTransaction('reference');
        $this->assertInstanceOf(QueryTransactionResponse::class, $response);
        $this->assertIsArray($response->getErrors());
        $this->assertNotEmpty($response->getErrors());
        $this->assertContains($errorCode, $response->getErrors());
    }

    /**
     * @return array[]
     */
    public function invalidResponseDataProvider(): array
    {
        return [
            [new Response(200, 'this is not json'), Client::ERROR_INVALID_JSON],
            [null, Client::ERROR_EMPTY_RESPONSE],
            [new Response(403, null, 'forbidden'), Client::ERROR_HTTP_AUTHENTICATION_ERROR],
            [new Response(503, null, 'service unavailable'), Client::ERROR_HTTP_SERVER_ERROR],
            [new Response(0, null, 'error'), Client::ERROR_CONNECTION_ERROR],
        ];
    }

    /**
     * @dataProvider emptyParametersDataProvider
     * @param string $method
     * @return void
     */
    public function testEmptyParameters(string $method): void
    {
        $response = $this->client->{$method}('');
        $this->assertInstanceOf(AbstractResponse::class, $response);
        $this->assertIsArray($response->getErrors());
        $this->assertNotEmpty($response->getErrors());
        $this->assertContains(Client::ERROR_INVALID_ARGUMENT, $response->getErrors());
    }

    /**
     * @return array[]
     */
    public function emptyParametersDataProvider(): array
    {
        return [
            ['queryTransaction'],
            ['queryInvoiceNumber'],
            ['queryInvoiceReference'],
            ['queryAccessCode'],
            ['queryCustomer'],
        ];
    }

    /**
     * @return void
     */
    public function testGetUriInvalid(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->client->getHttpService()->getUri(['path', null]);
    }

    /**
     * @return void
     */
    public function testCurlErrorNo(): void
    {
        $this->setUpCurl(400, null, 'error', 1);
        $response = $this->client->queryTransaction('reference');
        $this->assertInstanceOf(QueryTransactionResponse::class, $response);
        $this->assertIsArray($response->getErrors());
        $this->assertNotEmpty($response->getErrors());
    }
}
