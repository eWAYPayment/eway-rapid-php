<?php

namespace Eway\Test;

use Eway\Rapid;
use Eway\Rapid\Client;
use Eway\Rapid\Model\Support\HasCustomerTrait;
use Eway\Rapid\Model\Support\HasPaymentTrait;
use Eway\Rapid\Service\Http\Response;
use Psr\Log\LoggerInterface;

abstract class AbstractClientTest extends AbstractTest
{
    /**
     * @var Client
     */
    protected $client;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @return string
     */
    protected function getApiKey()
    {
        if (getenv('EWAY_API_KEY')) {
            return getenv('EWAY_API_KEY');
        } else {
            return '60CF3Ce97nRS1Z1Wp5m9kMmzHHEh8Rkuj31QCtVxjPWGYA9FymyqsK0Enm1P6mHJf0THbR';
        }
    }

    /**
     * @return string
     */
    protected function getApiPassword()
    {
        if (getenv('EWAY_API_PASSWORD')) {
            return getenv('EWAY_API_PASSWORD');
        } else {
            return 'API-P4ss';
        }
    }

    protected function setup()
    {
        parent::setUp();
        $this->client = Rapid::createClient($this->getApiKey(), $this->getApiPassword());
    }

    /**
     * @param                  $customer
     * @param HasCustomerTrait $response
     * @param bool             $cardWithinCustomer
     */
    protected function assertCustomer($customer, $response, $cardWithinCustomer = false)
    {
        foreach ($customer as $key => $value) {
            if (in_array($key, ['RedirectUrl', 'CancelUrl'])) {
                continue;
            }
            if ('CardDetails' === $key) {
                $this->assertCardDetails($value, $response, $cardWithinCustomer);
            } else {
                $this->assertEquals($value, $response->Customer->$key);
            }
        }
    }

    /**
     * @param array           $payment
     * @param HasPaymentTrait $response
     */
    protected function assertPayment($payment, $response)
    {
        foreach ($payment as $key => $value) {
            $this->assertEquals($value, $response->Payment->$key);
        }
    }

    /**
     * @param array            $cardDetails
     * @param HasCustomerTrait $response
     * @param bool             $cardWithinCustomer
     */
    protected function assertCardDetails($cardDetails, $response, $cardWithinCustomer = false)
    {
        foreach ($cardDetails as $key => $value) {
            if ('CVN' === $key) {
                // CVN will not be echoed back
                continue;
            } elseif ('Number' === $key) {
                if ($cardWithinCustomer) {
                    $this->assertEquals(substr($value, 0, 6), substr($response->Customer->CardNumber, 0, 6));
                    $this->assertEquals(substr($value, -4), substr($response->Customer->CardNumber, -4));
                } else {
                    $this->assertEquals(substr($value, 0, 6), substr($response->Customer->CardDetails->Number, 0, 6));
                    $this->assertEquals(substr($value, -4), substr($response->Customer->CardDetails->Number, -4));
                }
            } else {
                if ($cardWithinCustomer) {
                    $key = 'Card'.$key;
                    $this->assertEquals($value, $response->Customer->$key);
                } else {
                    $this->assertEquals($value, $response->Customer->CardDetails->$key);
                }
            }
        }
    }

    /**
     * @param string $data
     *
     * @return \Eway\Rapid\Service\Http\Response
     */
    protected function getResponse($data = '{}')
    {
        if (is_array($data)) {
            $data = json_encode($data);
        }

        return new Response(200, $data);
    }
}
