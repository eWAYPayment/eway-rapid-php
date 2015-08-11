<?php

namespace Eway\Rapid\Service;

use Eway\Rapid\Contract\Client;
use Eway\Rapid\Contract\Http\ResponseInterface;
use Eway\Rapid\Contract\HttpService as HttpServiceContract;
use Eway\Rapid\Service\Http\Response;
use InvalidArgumentException;

/**
 * Class HttpService.
 */
class Http implements HttpServiceContract
{
    /**
     * @var string
     */
    private $key;

    /**
     * @var string
     */
    private $password;

    /**
     * @var string
     */
    private $baseUrl;

    /**
     * @param $key
     * @param $password
     * @param $baseUrl
     */
    public function __construct($key = null, $password = null, $baseUrl = null)
    {
        $this->key = $key;
        $this->password = $password;
        $this->baseUrl = $baseUrl;
    }

    /**
     * @param $reference
     *
     * @return ResponseInterface
     */
    public function getTransaction($reference)
    {
        return $this->_getRequest([self::API_TRANSACTION_QUERY, ['Reference' => $reference]]);
    }

    /**
     * @param $invoiceNumber
     *
     * @return ResponseInterface
     */
    public function getTransactionInvoiceNumber($invoiceNumber)
    {
        return $this->_getRequest([self::API_TRANSACTION_INVOICE_NUMBER_QUERY, ['InvoiceNumber' => $invoiceNumber]]);
    }

    /**
     * @param $invoiceReference
     *
     * @return ResponseInterface
     */
    public function getTransactionInvoiceReference($invoiceReference)
    {
        return $this->_getRequest([self::API_TRANSACTION_INVOICE_REFERENCE_QUERY, ['InvoiceReference' => $invoiceReference]]);
    }

    /**
     * @param $data
     *
     * @return ResponseInterface
     */
    public function postTransaction($data)
    {
        return $this->_postRequest(self::API_TRANSACTION, $data);
    }

    /**
     * @param $transactionId
     * @param $data
     *
     * @return ResponseInterface
     */
    public function postTransactionRefund($transactionId, $data)
    {
        return $this->_postRequest([self::API_TRANSACTION_REFUND, ['TransactionID' => $transactionId]], $data);
    }

    /**
     * @param $data
     *
     * @return ResponseInterface
     */
    public function postAccessCodeShared($data)
    {
        return $this->_postRequest(self::API_ACCESS_CODE_SHARED, $data);
    }

    /**
     * @param $accessCode
     *
     * @return ResponseInterface
     */
    public function getAccessCode($accessCode)
    {
        return $this->_getRequest([self::API_ACCESS_CODE_QUERY, ['AccessCode' => $accessCode]]);
    }

    /**
     * @param $data
     *
     * @return ResponseInterface
     */
    public function postAccessCode($data)
    {
        return $this->_postRequest(self::API_ACCESS_CODE, $data);
    }

    /**
     * @param $tokenCustomerId
     *
     * @return ResponseInterface
     */
    public function getCustomer($tokenCustomerId)
    {
        return $this->_getRequest([self::API_CUSTOMER_QUERY, ['TokenCustomerID' => $tokenCustomerId]]);
    }

    /**
     * @param $data
     *
     * @return ResponseInterface
     */
    public function postCapturePayment($data)
    {
        return $this->_postRequest(self::API_CAPTURE_PAYMENT, $data);
    }

    /**
     * @param $data
     *
     * @return ResponseInterface
     */
    public function postCancelAuthorisation($data)
    {
        return $this->_postRequest(self::API_CANCEL_AUTHORISATION, $data);
    }

    /**
     * @param      $uri
     * @param bool $withBaseUrl
     *
     * @return mixed
     */
    public function getUri($uri, $withBaseUrl = true)
    {
        $baseUrl = $withBaseUrl ? $this->baseUrl : '';
        if (!is_array($uri)) {
            return $baseUrl.$uri;
        }
        list($uri, $vars) = $uri;
        $uri = $baseUrl.$uri;
        if (!is_array($vars)) {
            throw new InvalidArgumentException();
        }
        foreach ($vars as $key => $value) {
            $uri = str_replace('{'.$key.'}', $value, $uri);
        }

        return $uri;
    }

    /**
     * @param string $key
     *
     * @return Http
     */
    public function setKey($key)
    {
        $this->key = $key;

        return $this;
    }

    /**
     * @return string
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * @param string $password
     *
     * @return Http
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param string $baseUrl
     *
     * @return Http
     */
    public function setBaseUrl($baseUrl)
    {
        $this->baseUrl = $baseUrl;

        return $this;
    }

    /**
     * @return string
     */
    public function getBaseUrl()
    {
        return $this->baseUrl;
    }

    /**
     * @param $url
     *
     * @return ResponseInterface
     */
    private function _getRequest($url)
    {
        return $this->_request('GET', $url);
    }

    /**
     * @param $url
     * @param $data
     *
     * @return ResponseInterface
     */
    private function _postRequest($url, $data)
    {
        return $this->_request('POST', $url, $data);
    }

    /**
     * @param string $method
     * @param string $uri
     * @param array  $data
     *
     * @return ResponseInterface
     */
    private function _request($method, $uri, $data = [])
    {
        $uri = $this->getUri($uri);

        $headers = [];

        // Basic Auth
        $credentials = $this->key.':'.$this->password;

        // User Agent
        $agent = sprintf("%s %s", Client::NAME, Client::VERSION);

        $ch = curl_init();

        $options = [
            CURLOPT_URL => $uri,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HEADER => true,
            CURLOPT_USERAGENT => $agent,
            CURLOPT_USERPWD => $credentials,
            CURLOPT_TIMEOUT => 60,
        ];

        if (strtoupper($method) === 'POST') {
            $jsonData = json_encode($data);

            $headers[] = 'Content-Type: application/json';
            $headers[] = 'Content-Length: '.strlen($jsonData);

            $options[CURLOPT_CUSTOMREQUEST] = 'POST';
            $options[CURLOPT_POSTFIELDS] = $jsonData;
        }

        $options[CURLOPT_HTTPHEADER] = $headers;

        curl_setopt_array($ch, $options);

        $rawResponse = curl_exec($ch);

        $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        $responseBody = substr($rawResponse, $headerSize);

        $response = new Response($statusCode, $responseBody);

        curl_close($ch);

        return $response;
    }
}
