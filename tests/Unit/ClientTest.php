<?php

declare(strict_types=1);

namespace Eway\Test\Unit;

use Eway\Rapid\Client;
use Eway\Rapid\Contract\Client as ClientContract;
use Eway\Rapid\Enum\ApiMethod;
use Eway\Rapid\Exception\MassAssignmentException;
use Eway\Rapid\Exception\MethodNotImplementedException;
use Eway\Rapid\Model\Customer;
use Eway\Rapid\Model\Response\AbstractResponse;
use Eway\Rapid\Model\Response\CreateCustomerResponse;
use Eway\Rapid\Model\Response\CreateTransactionResponse;
use Eway\Rapid\Model\Transaction;
use Eway\Rapid\Service\Http;
use Eway\Rapid\Service\Http\Response;
use Eway\Rapid\Service\Logger;
use InvalidArgumentException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Throwable;

class ClientTest extends TestCase
{
    /** @var Client $client */
    private $client;

    /** @var Http|MockObject $http */
    private $http;

    /** @var Logger|MockObject $logger */
    private $logger;

    /**
     * @return void
     */
    protected function setUp(): void
    {
        $this->http = $this->createMock(Http::class);
        $this->logger = $this->createMock(Logger::class);
        $this->client = new Client('api-key', 'api-password', ClientContract::MODE_SANDBOX, $this->logger);
        $this->client->setHttpService($this->http);
    }

    /**
     * @dataProvider getterDataProvider
     * @param string $method
     * @param $value
     * @return void
     */
    public function testGetters(string $method, $value): void
    {
        $this->assertEquals($value, $this->client->{$method}());
    }

    /**
     * @return array[]
     */
    public function getterDataProvider(): array
    {
        return [
            ['isValid', true],
            ['getErrors', []],
            ['getEndpoint', ClientContract::ENDPOINT_SANDBOX],
        ];
    }

    /**
     * @dataProvider endpointDataProvider
     * @param string $expected
     * @param string $value
     * @return void
     */
    public function testSetEndpoint(string $expected, string $value): void
    {
        $this->http->expects($this->once())->method('setBaseUrl');
        $this->client->setEndpoint($value);
        $this->assertEquals($expected, $this->client->getEndpoint());
    }

    /**
     * @return array
     */
    public function endpointDataProvider(): array
    {
        return [
            [ClientContract::ENDPOINT_PRODUCTION, ClientContract::MODE_PRODUCTION],
            [ClientContract::ENDPOINT_SANDBOX, ClientContract::MODE_SANDBOX],
            ['https://example.com/', 'https://example.com/'],
        ];
    }

    /**
     * @return void
     */
    public function testSetVersion(): void
    {
        $this->http->expects($this->once())->method('setVersion')->with(1);
        $this->client->setVersion(1);
    }

    /**
     * @return void
     */
    public function testValidateCredentials(): void
    {
        $this->http->expects($this->once())->method('setKey');
        $this->http->expects($this->once())->method('setPassword');
        $this->logger->expects($this->once())->method('error');
        $this->client->setCredential('', '');
        $this->assertContains(ClientContract::ERROR_INVALID_CREDENTIAL, $this->client->getErrors());
    }

    /**
     * @return void
     */
    public function testValidateEndpoint(): void
    {
        $this->http->expects($this->once())->method('setBaseUrl');
        $this->logger->expects($this->once())->method('error');
        $this->client->setEndpoint('');
        $this->assertContains(ClientContract::ERROR_INVALID_ENDPOINT, $this->client->getErrors());
    }

    /**
     * @dataProvider createTransactionDataProvider
     * @param string $apiMethod
     * @param string $method
     * @param bool $capture
     * @param bool $save
     * @return void
     */
    public function testCreateTransaction(string $apiMethod, string $method, bool $capture, bool $save): void
    {
        $transaction = new Transaction([
            'Capture' => $capture,
            'SaveCustomer' => $save,
        ]);

        $this->http->expects($this->once())->method($method)->willReturn(new Response(200, '{"response":"ok"}'));
        $this->assertEquals(
            new CreateTransactionResponse(),
            $this->client->createTransaction($apiMethod, $transaction),
        );
    }

    /**
     * @return array[]
     */
    public function createTransactionDataProvider(): array
    {
        return [
            [ApiMethod::DIRECT, 'postTransaction', false, false],
            [ApiMethod::WALLET, 'postTransaction', true, true],
            [ApiMethod::RESPONSIVE_SHARED, 'postAccessCodeShared', false, false],
            [ApiMethod::RESPONSIVE_SHARED, 'postAccessCodeShared', true, false],
            [ApiMethod::RESPONSIVE_SHARED, 'postAccessCodeShared', true, true],
            [ApiMethod::TRANSPARENT_REDIRECT, 'postAccessCode', false, false],
            [ApiMethod::TRANSPARENT_REDIRECT, 'postAccessCode', true, false],
            [ApiMethod::TRANSPARENT_REDIRECT, 'postAccessCode', true, true],
            [ApiMethod::AUTHORISATION, 'postCapturePayment', false, false],
        ];
    }

    /**
     * @dataProvider transactionsDataProvider
     * @param string $clientMethod
     * @param string $httpMethod
     * @param mixed $params
     * @return void
     */
    public function testDoTransactions(string $clientMethod, string $httpMethod, $params): void
    {
        $this->http->expects($this->once())->method($httpMethod)->willReturn(new Response(200, '{"response":"ok"}'));
        $response = call_user_func_array([$this->client, $clientMethod], $params);
        $this->assertInstanceOf(AbstractResponse::class, $response);
    }

    /**
     * @return array[]
     */
    public function transactionsDataProvider(): array
    {
        return [
            ['create3dsEnrolment', 'post3dsEnrolment', [new Transaction()]],
            ['verify3dsEnrolment', 'post3dsEnrolmentVerification', [new Transaction()]],
            ['queryTransaction', 'getTransaction', [new Transaction()]],
            ['queryInvoiceNumber', 'getTransactionInvoiceNumber', [new Transaction()]],
            ['queryInvoiceReference', 'getTransactionInvoiceReference', [new Transaction()]],
            ['queryCustomer', 'getCustomer', [new Transaction()]],
            ['refund', 'postTransactionRefund', [['Refund' => ['TransactionID' => '12345']]]],
            ['cancelTransaction', 'postCancelAuthorisation', [new Transaction()]],
            ['queryAccessCode', 'getAccessCode', [new Transaction()]],
            ['settlementSearch', 'getSettlementSearch', [['value']]],
        ];
    }

    /**
     * @dataProvider createCustomerDataProvider
     * @param string $apiMethod
     * @param string $httpMethod
     * @return void
     */
    public function testCreateCustomer(string $apiMethod, string $httpMethod): void
    {
        $this->http->expects($this->once())->method($httpMethod)->willReturn(new Response(200, '{"response":"ok"}'));
        $this->assertEquals(new CreateCustomerResponse(), $this->client->createCustomer($apiMethod, [
            'PaymentInstrument' => 'instrument',
            'TokenCustomerID' => 'token-customer-id',
            'Title' => 'Mr.',
        ]));
    }

    /**
     * @return array[]
     */
    public function createCustomerDataProvider(): array
    {
        return [
            [ApiMethod::DIRECT, 'postTransaction'],
            [ApiMethod::WALLET, 'postTransaction'],
            [ApiMethod::RESPONSIVE_SHARED, 'postAccessCodeShared'],
            [ApiMethod::TRANSPARENT_REDIRECT, 'postAccessCode'],
        ];
    }

    /**
     * @return void
     */
    public function testCreateCustomerInvalid(): void
    {
        $this->expectException(MethodNotImplementedException::class);
        $this->client->createCustomer(ApiMethod::AUTHORISATION, []);
    }

    /**
     * @dataProvider updateCustomerDataProvider
     * @param string $apiMethod
     * @param string $httpMethod
     * @return void
     */
    public function testUpdateCustomer(string $apiMethod, string $httpMethod): void
    {
        $this->http->expects($this->once())->method($httpMethod)->willReturn(new Response(200, '{"response":"ok"}'));
        $this->assertInstanceOf(
            AbstractResponse::class,
            $this->client->updateCustomer($apiMethod, new Customer()),
        );
    }

    /**
     * @return array[]
     */
    public function updateCustomerDataProvider(): array
    {
        return [
            [ApiMethod::DIRECT, 'postTransaction'],
            [ApiMethod::RESPONSIVE_SHARED, 'postAccessCodeShared'],
            [ApiMethod::TRANSPARENT_REDIRECT, 'postAccessCode'],
        ];
    }

    /**
     * @return void
     */
    public function testUpdateCustomerInvalid(): void
    {
        $this->expectException(MethodNotImplementedException::class);
        $this->client->updateCustomer(ApiMethod::AUTHORISATION, new Customer());
    }

    /**
     * @return void
     */
    public function testInvokeInvalid(): void
    {
        $this->client->setCredential('', '');
        $response = $this->client->createTransaction(ApiMethod::AUTHORISATION, new Transaction());
        $this->assertFalse($this->client->isValid());
        $this->assertNotEmpty($response->getErrors());
    }

    /**
     * @dataProvider throwExceptionDataProvider
     * @param string $errorCode
     * @param Throwable $exception
     * @return void
     */
    public function testInvokeDoThrowException(string $errorCode, Throwable $exception): void
    {
        $this->http->expects($this->once())->method('getTransactionInvoiceNumber')->willThrowException($exception);
        $response = $this->client->queryInvoiceNumber('invoice-number');
        $this->assertNotEmpty($response->getErrors());
        $this->assertContains($errorCode, $response->getErrors());
    }

    /**
     * @return array[]
     */
    public function throwExceptionDataProvider(): array
    {
        return [
            [ClientContract::ERROR_INVALID_ARGUMENT, new InvalidArgumentException()],
            [ClientContract::ERROR_INVALID_ARGUMENT, new MassAssignmentException()],
        ];
    }

    /**
     * @dataProvider notJsonResponseDataProvider
     * @param Response|null $response
     * @param string $errorCode
     * @return void
     */
    public function testNotJsonResponse(?Response $response, string $errorCode): void
    {
        $this->logger->expects($this->once())->method('error');
        $this->http->expects($this->once())->method('getTransaction')->willReturn($response);
        $clientResponse = $this->client->queryTransaction('reference');
        $this->assertContains($errorCode, $clientResponse->getErrors());
    }

    /**
     * @return array[]
     */
    public function notJsonResponseDataProvider(): array
    {
        return [
            [new Response(200, '<h1>Not json</h1>'), ClientContract::ERROR_INVALID_JSON],
            [null, ClientContract::ERROR_EMPTY_RESPONSE],
            [new Response(503, 'error'), ClientContract::ERROR_HTTP_SERVER_ERROR],
            [new Response(403, 'error'), ClientContract::ERROR_HTTP_AUTHENTICATION_ERROR],
            [new Response(0, '', 'error'), ClientContract::ERROR_CONNECTION_ERROR],
        ];
    }
}
