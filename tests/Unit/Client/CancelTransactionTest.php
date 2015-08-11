<?php

namespace Eway\Test\Unit\Client;

use Eway\Rapid\Contract\HttpService;
use Eway\Rapid\Model\Response\RefundResponse;
use Eway\Test\AbstractClientTest;
use Prophecy\Argument;
use Prophecy\Prophecy\MethodProphecy;

class CancelTransactionTest extends AbstractClientTest
{
    /**
     * Cancel Authorised Transaction
     */
    public function testCancelTransactionShouldCallHttpServicePostCancelAuthorisation()
    {
        $mockTransactionId = 12345678;

        $mockResponse = $this->getResponse([
            "ResponseCode" => "123456",
            "ResponseMessage" => "234567",
            "TransactionID" => 12345679,
            "TransactionStatus" => true,
            "Errors" => "",
        ]);

        $httpService = $this->prophet->prophesize('Eway\Rapid\Contract\HttpService');

        /** @var MethodProphecy $postCancelAuthorisationStub */
        $postCancelAuthorisationStub = $httpService->postCancelAuthorisation(Argument::type('array'));
        $postCancelAuthorisationStub->withArguments([['TransactionID' => $mockTransactionId]])->willReturn($mockResponse)->shouldBeCalled();

        /** @var HttpService $stub */
        $stub = $httpService->reveal();
        $this->client->setHttpService($stub);

        $response = $this->client->cancelTransaction($mockTransactionId);

        $this->assertInstanceOf(RefundResponse::getClass(), $response);
        $this->assertTrue(is_array($response->getErrors()));
        $this->assertEmpty($response->getErrors());
        $this->assertTrue($response->TransactionStatus);
    }
}
