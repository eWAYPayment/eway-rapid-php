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
     *
     * @var int
     */
    private $version;

    /**
     * Extra proxy "Connection Established" header text
     */
    private static $CONNECTION_ESTABLISHED_HEADERS = array(
        "HTTP/1.0 200 Connection established\r\n\r\n",
        "HTTP/1.1 200 Connection established\r\n\r\n",
    );

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
        if (empty($reference)) {
            throw new InvalidArgumentException();
        }

        return $this->getRequest([
            self::API_TRANSACTION_QUERY,
            ['Reference' => $reference]
        ]);
    }

    /**
     * @param $invoiceNumber
     *
     * @return ResponseInterface
     */
    public function getTransactionInvoiceNumber($invoiceNumber)
    {
        if (empty($invoiceNumber)) {
            throw new InvalidArgumentException();
        }

        return $this->getRequest([
            self::API_TRANSACTION_INVOICE_NUMBER_QUERY,
            ['InvoiceNumber' => $invoiceNumber]
        ]);
    }

    /**
     * @param $invoiceReference
     *
     * @return ResponseInterface
     */
    public function getTransactionInvoiceReference($invoiceReference)
    {
        if (empty($invoiceReference)) {
            throw new InvalidArgumentException();
        }

        return $this->getRequest([
            self::API_TRANSACTION_INVOICE_REFERENCE_QUERY,
            ['InvoiceReference' => $invoiceReference]
        ]);
    }

    /**
     * @param $data
     *
     * @return ResponseInterface
     */
    public function postTransaction($data)
    {
        return $this->postRequest(self::API_TRANSACTION, $data);
    }

    /**
     * @param $transactionId
     * @param $data
     *
     * @return ResponseInterface
     */
    public function postTransactionRefund($transactionId, $data)
    {
        return $this->postRequest(
            [
                self::API_TRANSACTION_REFUND,
                ['TransactionID' => $transactionId]
            ],
            $data
        );
    }

    /**
     * @param $data
     *
     * @return ResponseInterface
     */
    public function postAccessCodeShared($data)
    {
        return $this->postRequest(self::API_ACCESS_CODE_SHARED, $data);
    }

    /**
     * @param $data
     *
     * @return ResponseInterface
     */
    public function post3dsEnrolment($data)
    {
        return $this->postRequest(self::API_3DS2_CREATE_ENROLMENT, $data);
    }

    /**
     * @param $data
     *
     * @return ResponseInterface
     */
    public function post3dsEnrolmentVerification($data)
    {
        return $this->postRequest(self::API_3DS2_VERIFY_ENROLMENT, $data);
    }

    /**
     * @param $accessCode
     *
     * @return ResponseInterface
     */
    public function getAccessCode($accessCode)
    {
        if (empty($accessCode)) {
            throw new InvalidArgumentException();
        }

        return $this->getRequest([
            self::API_ACCESS_CODE_QUERY,
            ['AccessCode' => $accessCode]
        ]);
    }

    /**
     * @param $data
     *
     * @return ResponseInterface
     */
    public function postAccessCode($data)
    {
        return $this->postRequest(self::API_ACCESS_CODE, $data);
    }

    /**
     * @param $tokenCustomerId
     *
     * @return ResponseInterface
     */
    public function getCustomer($tokenCustomerId)
    {
        if (empty($tokenCustomerId)) {
            throw new InvalidArgumentException();
        }

        return $this->getRequest([
            self::API_CUSTOMER_QUERY,
            ['TokenCustomerID' => $tokenCustomerId]
        ]);
    }

    /**
     * @param $data
     *
     * @return ResponseInterface
     */
    public function postCapturePayment($data)
    {
        return $this->postRequest(self::API_CAPTURE_PAYMENT, $data);
    }

    /**
     * @param $data
     *
     * @return ResponseInterface
     */
    public function postCancelAuthorisation($data)
    {
        return $this->postRequest(self::API_CANCEL_AUTHORISATION, $data);
    }

    /**
     * @param $query
     *
     * @return ResponseInterface
     */
    public function getSettlementSearch($query)
    {
        return $this->getRequest(self::API_SETTLEMENT_SEARCH, $query);
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
     *
     * @param int $version
     * @return Http
     */
    public function setVersion($version)
    {
        $this->version = $version;

        return $this;
    }

    /**
     *
     * @return int
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * @param $url
     *
     * @return ResponseInterface
     */
    private function getRequest($url, $query = [])
    {
        return $this->request('GET', $url, $query);
    }

    /**
     * @param $url
     * @param $data
     *
     * @return ResponseInterface
     */
    private function postRequest($url, $data)
    {
        return $this->request('POST', $url, $data);
    }

    /**
     * @param string $method
     * @param string $uri
     * @param array  $data
     *
     * @return ResponseInterface
     */
    private function request($method, $uri, $data = [])
    {
        $uri = $this->getUri($uri);

        $headers = [];

        // Basic Auth
        $credentials = $this->key.':'.$this->password;

        // User Agent
        $agent = sprintf("%s %s", Client::NAME, Client::VERSION);

        $ch = curl_init();

        if (strtoupper($method) === 'GET' && !empty($data)) {
            $queryString = http_build_query($data);
            $uri .= '?'.$queryString;
        }

        $options = [
            CURLOPT_URL => $uri,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HEADER => true,
            CURLOPT_USERAGENT => $agent,
            CURLOPT_USERPWD => $credentials,
            CURLOPT_TIMEOUT => 120,
        ];

        if (strtoupper($method) === 'POST') {
            $jsonData = json_encode($data);

            $headers[] = 'Content-Type: application/json';
            $headers[] = 'Content-Length: '.strlen($jsonData);

            $options[CURLOPT_CUSTOMREQUEST] = 'POST';
            $options[CURLOPT_POSTFIELDS] = $jsonData;
        }

        if (isset($this->version) && is_numeric($this->version)) {
            $headers[] = 'X-EWAY-APIVERSION: '.$this->version;
        }

        $options[CURLOPT_HTTPHEADER] = $headers;

        curl_setopt_array($ch, $options);

        $rawResponse = curl_exec($ch);

        $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);

        if (curl_errno($ch)) {
            $responseError = curl_error($ch);
            $responseBody = '';
        } else {
            $responseError = '';
            $responseBody = $this->parseResponse($rawResponse, $headerSize);
        }

        $response = new Response($statusCode, $responseBody, $responseError);

        curl_close($ch);

        return $response;
    }

     /**
     * Returns the HTTP body from raw response.
     *
     * @param string $rawResponse
     * @param string $headerSize
     * @return string
     */
    private function parseResponse($rawResponse, $headerSize)
    {
        foreach (self::$CONNECTION_ESTABLISHED_HEADERS as $established_header) {
            if (stripos($rawResponse, $established_header) !== false) {
                $rawResponse = str_ireplace($established_header, '', $rawResponse);
                // Older cURL versions did not account for proxy headers in the
                // header size
                if (!$this->needsCurlProxyFix()) {
                    $headerSize -= strlen($established_header);
                }
                break;
            }
        }

        $responseBody = substr($rawResponse, $headerSize);

        return $responseBody;
    }

    /**
     * Detect versions of cURL which report incorrect header lengths when
     * using a proxy
     *
     * @return boolean
     */
    private function needsCurlProxyFix()
    {
        $ver = curl_version();
        $versionNum = $ver['version_number'];
        return $versionNum < self::CURL_NO_QUIRK_VERSION;
    }
}
