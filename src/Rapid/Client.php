<?php

namespace Eway\Rapid;

use Eway\Rapid\Contract\Client as ClientContract;
use Eway\Rapid\Contract\Http\ResponseInterface;
use Eway\Rapid\Contract\HttpService as HttpServiceContract;
use Eway\Rapid\Enum\ApiMethod;
use Eway\Rapid\Enum\PaymentMethod;
use Eway\Rapid\Enum\TransactionType;
use Eway\Rapid\Enum\LogLevel;
use Eway\Rapid\Exception\MassAssignmentException;
use Eway\Rapid\Exception\MethodNotImplementedException;
use Eway\Rapid\Exception\RequestException;
use Eway\Rapid\Model\Customer;
use Eway\Rapid\Model\Refund;
use Eway\Rapid\Model\Response\AbstractResponse;
use Eway\Rapid\Model\Response\Creation3dsEnrolmentResponse;
use Eway\Rapid\Model\Response\CreateCustomerResponse;
use Eway\Rapid\Model\Response\CreateTransactionResponse;
use Eway\Rapid\Model\Response\QueryAccessCodeResponse;
use Eway\Rapid\Model\Response\QueryCustomerResponse;
use Eway\Rapid\Model\Response\QueryTransactionResponse;
use Eway\Rapid\Model\Response\RefundResponse;
use Eway\Rapid\Model\Response\SettlementSearchResponse;
use Eway\Rapid\Model\Response\Verification3dsEnrolmentResponse;
use Eway\Rapid\Model\Transaction;
use Eway\Rapid\Service\Http;
use Eway\Rapid\Validator\ClassValidator;
use Eway\Rapid\Validator\EnumValidator;

use InvalidArgumentException;

/**
 * eWAY Rapid Client
 *
 * Connect to eWAY's Rapid API to process transactions and refunds, create and
 * update customer tokens.
 *
 */
class Client implements ClientContract
{
    /**
     * Rapid API Key
     *
     * @var string
     */
    private $apiKey;

    /**
     * Password for the API Key
     *
     * @var string
     */
    private $apiPassword;

    /**
     * Possible values ("Production", "Sandbox", or a URL) "Production" and "Sandbox"
     * will default to the Global Rapid API Endpoints.
     *
     * @var string
     */
    private $endpoint;

    /**
     * The eWAY Rapid API version to be used.
     *
     *
     * @var string
     */
    private $version;

    /**
     * True if the Client has a valid API Key, Password and Endpoint Set.
     *
     * @var bool
     */
    private $isValid = false;

    /**
     * Contains the Rapid Error code in the case of initialisation errors.
     *
     * @var array
     */
    private $errors = [];

    /**
     * @var HttpServiceContract
     */
    private $httpService;

    /**
     * @var Psr\Log\LoggerInterface
     */
    private $logger;

    /**
     * @param string $apiKey
     * @param string $apiPassword
     * @param string $endpoint
     * @param Psr\Log\LoggerInterface $logger PSR-3 logger
     */
    public function __construct($apiKey, $apiPassword, $endpoint, $logger = null)
    {
        if (isset($logger)) {
            $this->setLogger($logger);
        }
        $this->setHttpService(new Http());
        $this->setCredential($apiKey, $apiPassword);
        $this->setEndpoint($endpoint);
    }

    #region Public Functions

    /**
     * @inheritdoc
     */
    public function isValid()
    {
        return $this->isValid;
    }

    /**
     * @inheritdoc
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * @inheritdoc
     */
    public function getEndpoint()
    {
        return $this->endpoint;
    }

    /**
     * @inheritdoc
     */
    public function setEndpoint($endpoint)
    {
        if (ClientContract::MODE_SANDBOX === strtolower($endpoint)) {
            $endpoint = ClientContract::ENDPOINT_SANDBOX;
        } elseif (ClientContract::MODE_PRODUCTION === strtolower($endpoint)) {
            $endpoint = ClientContract::ENDPOINT_PRODUCTION;
        }

        $this->endpoint = $endpoint;
        $this->getHttpService()->setBaseUrl($endpoint);
        $this->validateEndpoint();

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function setVersion($version)
    {
        $this->version = $version;
        $this->getHttpService()->setVersion($version);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function setCredential($apiKey, $apiPassword)
    {
        $this->apiKey = $apiKey;
        $this->apiPassword = $apiPassword;
        $this->getHttpService()->setKey($apiKey);
        $this->getHttpService()->setPassword($apiPassword);
        $this->validateCredentials();

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function setLogger($logger)
    {
        $this->logger = $logger;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function createTransaction($apiMethod, $transaction)
    {
        return $this->invoke(CreateTransactionResponse::getClass());
    }

    /**
     * @inheritdoc
     */
    public function queryTransaction($reference)
    {
        return $this->invoke(QueryTransactionResponse::getClass());
    }

    /**
     * @inheritdoc
     */
    public function queryInvoiceNumber($invoiceNumber)
    {
        return $this->invoke(QueryTransactionResponse::getClass());
    }

    /**
     * @inheritdoc
     */
    public function queryInvoiceReference($invoiceReference)
    {
        return $this->invoke(QueryTransactionResponse::getClass());
    }

    /**
     * @inheritdoc
     */
    public function createCustomer($apiMethod, $customer)
    {
        return $this->invoke(CreateCustomerResponse::getClass());
    }

    /**
     * @inheritdoc
     */
    public function updateCustomer($apiMethod, $customer)
    {
        return $this->invoke(CreateCustomerResponse::getClass());
    }

    /**
     * @inheritdoc
     */
    public function queryCustomer($tokenCustomerId)
    {
        return $this->invoke(QueryCustomerResponse::getClass());
    }

    /**
     * @inheritdoc
     */
    public function refund($refund)
    {
        return $this->invoke(RefundResponse::getClass());
    }

    /**
     * @inheritdoc
     */
    public function cancelTransaction($transactionId)
    {
        return $this->invoke(RefundResponse::getClass());
    }

    /**
     * @param mixed $accessCode
     *
     * @return QueryAccessCodeResponse
     */
    public function queryAccessCode($accessCode)
    {
        return $this->invoke(QueryAccessCodeResponse::getClass());
    }

    /**
     * @inheritdoc
     */
    public function settlementSearch($query)
    {
        return $this->invoke(SettlementSearchResponse::getClass());
    }

    /**
     * @param $transaction
     * @return AbstractResponse
     */
    public function create3dsEnrolment($transaction)
    {
        return $this->invoke(Creation3dsEnrolmentResponse::getClass());
    }

    /**
     * @param $transaction
     * @return AbstractResponse
     */
    public function verify3dsEnrolment($transaction)
    {
        return $this->invoke(Verification3DsEnrolmentResponse::getClass());
    }

    #endregion

    #region Getter/Setter

    /**
     * @param HttpServiceContract $httpService
     *
     * @return Client
     */
    public function setHttpService(HttpServiceContract $httpService)
    {
        $this->httpService = $httpService;

        return $this;
    }

    /**
     * @return HttpServiceContract
     */
    public function getHttpService()
    {
        return $this->httpService;
    }

    #endregion

    #region Internal logic

    /**
     * @param $apiMethod
     * @param $transaction
     *
     * @return ResponseInterface
     */
    private function doCreateTransaction($apiMethod, $transaction)
    {
        $apiMethod = EnumValidator::validate('Eway\Rapid\Enum\ApiMethod', 'ApiMethod', $apiMethod);

        /** @var Transaction $transaction */
        $transaction = ClassValidator::getInstance('Eway\Rapid\Model\Transaction', $transaction);

        switch ($apiMethod) {
            case ApiMethod::DIRECT:
            case ApiMethod::WALLET:
                if ($transaction->Capture) {
                    $transaction->Method = PaymentMethod::PROCESS_PAYMENT;
                } else {
                    $transaction->Method = PaymentMethod::AUTHORISE;
                }

                return $this->getHttpService()->postTransaction($transaction->toArray());

            case ApiMethod::RESPONSIVE_SHARED:
                if ($transaction->Capture) {
                    if ((isset($transaction->Customer) && isset($transaction->Customer->TokenCustomerID))
                        || (isset($transaction->SaveCustomer) && $transaction->SaveCustomer == true)) {
                        $transaction->Method = PaymentMethod::TOKEN_PAYMENT;
                    } else {
                        $transaction->Method = PaymentMethod::PROCESS_PAYMENT;
                    }
                } else {
                    $transaction->Method = PaymentMethod::AUTHORISE;
                }

                return $this->getHttpService()->postAccessCodeShared($transaction->toArray());

            case ApiMethod::TRANSPARENT_REDIRECT:
                if ($transaction->Capture) {
                    if ((isset($transaction->Customer) && isset($transaction->Customer->TokenCustomerID))
                        || (isset($transaction->SaveCustomer) && $transaction->SaveCustomer == true)) {
                        $transaction->Method = PaymentMethod::TOKEN_PAYMENT;
                    } else {
                        $transaction->Method = PaymentMethod::PROCESS_PAYMENT;
                    }
                } else {
                    $transaction->Method = PaymentMethod::AUTHORISE;
                }

                return $this->getHttpService()->postAccessCode($transaction->toArray());

            case ApiMethod::AUTHORISATION:
                return $this->getHttpService()->postCapturePayment($transaction->toArray());

            default:
                // Although right now this code is not reachable, protect against incomplete
                // changes to ApiMethod
                throw new MethodNotImplementedException();
        }
    }

    /**
     * @param $transaction
     * @return ResponseInterface
     */
    private function doCreate3dsEnrolment($transaction)
    {
        return $this->getHttpService()->post3dsEnrolment($transaction);
    }

    /**
     * @param $transaction
     * @return ResponseInterface
     */
    private function doVerify3dsEnrolment($transaction)
    {
        return $this->getHttpService()->post3dsEnrolmentVerification($transaction);
    }

    /**
     * @param $reference
     *
     * @return ResponseInterface
     */
    private function doQueryTransaction($reference)
    {
        return $this->getHttpService()->getTransaction($reference);
    }

    /**
     * @param $invoiceNumber
     *
     * @return ResponseInterface
     */
    private function doQueryInvoiceNumber($invoiceNumber)
    {
        return $this->getHttpService()->getTransactionInvoiceNumber($invoiceNumber);
    }


    /**
     * @param $invoiceReference
     *
     * @return ResponseInterface
     */
    private function doQueryInvoiceReference($invoiceReference)
    {
        return $this->getHttpService()->getTransactionInvoiceReference($invoiceReference);
    }

    /**
     * @param $apiMethod
     * @param $customer
     *
     * @return ResponseInterface
     */
    private function doCreateCustomer($apiMethod, $customer)
    {
        $paymentInstrument = $customer['PaymentInstrument'] ?? null;

        /** @var Customer $customer */
        $customer = ClassValidator::getInstance('Eway\Rapid\Model\Customer', $customer);

        $apiMethod = EnumValidator::validate('Eway\Rapid\Enum\ApiMethod', 'ApiMethod', $apiMethod);

        $transaction = $this->customerToTransaction($customer);
        $transaction->Method = PaymentMethod::CREATE_TOKEN_CUSTOMER;

        if ($apiMethod == ApiMethod::WALLET && $paymentInstrument) {
            $transaction->PaymentInstrument = $paymentInstrument;
            $transaction->Payment = ['TotalAmount' => 0];
        }

        switch ($apiMethod) {
            case ApiMethod::DIRECT:
            case ApiMethod::WALLET:

                return $this->getHttpService()->postTransaction($transaction->toArray());

            case ApiMethod::RESPONSIVE_SHARED:
                $transaction->Payment = ['TotalAmount' => 0];

                return $this->getHttpService()->postAccessCodeShared($transaction->toArray());

            case ApiMethod::TRANSPARENT_REDIRECT:
                $transaction->Payment = ['TotalAmount' => 0];

                return $this->getHttpService()->postAccessCode($transaction->toArray());

            default:
                // Although right now this code is not reachable, protect against incomplete
                // changes to ApiMethod
                throw new MethodNotImplementedException();
        }
    }

    /**
     *
     * @param $apiMethod
     * @param type $customer
     * @return ResponseInterface
     * @throws MethodNotImplementedException
     */
    private function doUpdateCustomer($apiMethod, $customer)
    {
        /** @var Customer $customer */
        $customer = ClassValidator::getInstance('Eway\Rapid\Model\Customer', $customer);

        $apiMethod = EnumValidator::validate('Eway\Rapid\Enum\ApiMethod', 'ApiMethod', $apiMethod);

        $transaction = $this->customerToTransaction($customer);
        $transaction->Method = PaymentMethod::UPDATE_TOKEN_CUSTOMER;
        $transaction->Payment = ['TotalAmount' => 0];

        switch ($apiMethod) {
            case ApiMethod::DIRECT:
                return $this->getHttpService()->postTransaction($transaction->toArray());

            case ApiMethod::RESPONSIVE_SHARED:
                return $this->getHttpService()->postAccessCodeShared($transaction->toArray());

            case ApiMethod::TRANSPARENT_REDIRECT:
                return $this->getHttpService()->postAccessCode($transaction->toArray());

            default:
                // Although right now this code is not reachable, protect against incomplete
                // changes to ApiMethod
                throw new MethodNotImplementedException();
        }
    }

    /**
     * @param $tokenCustomerId
     *
     * @return ResponseInterface
     */
    private function doQueryCustomer($tokenCustomerId)
    {
        return $this->getHttpService()->getCustomer($tokenCustomerId);
    }

    /**
     * @param $refund
     *
     * @return ResponseInterface
     */
    private function doRefund($refund)
    {
        /** @var Refund $refund */
        $refund = ClassValidator::getInstance('Eway\Rapid\Model\Refund', $refund);

        return $this->getHttpService()->postTransactionRefund($refund->Refund->TransactionID, $refund->toArray());
    }

    /**
     * @param $transactionId
     *
     * @return ResponseInterface
     */
    private function doCancelTransaction($transactionId)
    {
        $refund = [
            'TransactionID' => $transactionId,
        ];

        /** @var Refund $refund */
        $refund = ClassValidator::getInstance('Eway\Rapid\Model\Refund', $refund);

        return $this->getHttpService()->postCancelAuthorisation($refund->toArray());
    }

    /**
     * @param $accessCode
     *
     * @return ResponseInterface
     */
    private function doQueryAccessCode($accessCode)
    {
        return $this->getHttpService()->getAccessCode($accessCode);
    }

    /**
     * @param $query
     *
     * @return ResponseInterface
     */
    private function doSettlementSearch($query)
    {
        $search = ClassValidator::getInstance('Eway\Rapid\Model\SettlementSearch', $query);
        return $this->getHttpService()->getSettlementSearch($search->toArray());
    }

    #endregion

    #region Internal helpers

    /**
     * @param $responseClass
     *
     * @return AbstractResponse
     */
    private function invoke($responseClass)
    {
        if (!$this->isValid()) {
            return $this->getErrorResponse($responseClass);
        }

        try {
            $caller = $this->getCaller();
            $response = call_user_func_array([$this, 'do'.ucfirst($caller['function'])], $caller['args']);

            return $this->wrapResponse($responseClass, $response);
        } catch (InvalidArgumentException $e) {
            $this->addError(self::ERROR_INVALID_ARGUMENT);
        } catch (MassAssignmentException $e) {
            $this->addError(self::ERROR_INVALID_ARGUMENT);
        }


        return $this->getErrorResponse($responseClass);
    }

    /**
     * @return mixed
     */
    private function getCaller()
    {
        $callers = debug_backtrace(DEBUG_BACKTRACE_PROVIDE_OBJECT, 3);

        // Index of the caller of this function is 1
        // So caller of that caller is 2
        return $callers[2];
    }

    /**
     * @param string            $class
     * @param ResponseInterface $httpResponse
     *
     * @return mixed
     */
    private function wrapResponse($class, $httpResponse = null)
    {
        $data = [];
        try {
            if (isset($httpResponse)) {
                $this->checkResponse($httpResponse);
                $body = (string)$httpResponse->getBody();
                if (!$this->isJson($body)) {
                    $this->log('error', "Response is not valid JSON");
                    $this->addError(self::ERROR_INVALID_JSON);
                } else {
                    $data = json_decode($body, true);
                }
            } else {
                $this->log('error', "Response from gateway is empty");
                $this->addError(self::ERROR_EMPTY_RESPONSE);
            }
        } catch (RequestException $e) {
            // An error code is already provided by checkResponse
        }

        /** @var AbstractResponse $response */
        $response = new $class($data);
        foreach ($this->getErrors() as $errorCode) {
            $response->addError($errorCode);
        }

        return $response;
    }

    /**
     * @param $string
     *
     * @return bool
     */
    private function isJson($string)
    {
        return is_string($string) && is_object(json_decode($string)) && (json_last_error() == JSON_ERROR_NONE);
    }

    /**
     * @return $this
     */
    private function emptyErrors()
    {
        $this->errors = [];

        return $this;
    }

    /**
     *
     * @return $this
     */
    public function validateCredentials()
    {
        $this->removeError(self::ERROR_INVALID_CREDENTIAL);
        if (empty($this->apiKey) || empty($this->apiPassword)) {
            $this->log('error', "Missing API key or password");
            $this->addError(self::ERROR_INVALID_CREDENTIAL, false);
        }

        if (empty($this->errors)) {
            $this->isValid = true;
        }

        return $this;
    }

    /**
     *
     * @return $this
     */
    public function validateEndpoint()
    {
        $this->removeError(self::ERROR_INVALID_ENDPOINT);
        if (empty($this->endpoint)
            || strpos($this->endpoint, 'https') !== 0
            || substr($this->endpoint, -1) != '/') {
            $this->log('error', "Missing or invalid endpoint");
            $this->addError(self::ERROR_INVALID_ENDPOINT, false);
        }

        if (empty($this->errors)) {
            $this->isValid = true;
        }

        return $this;
    }

    /**
     * @param string $errorCode
     *
     * @return $this
     */
    private function addError($errorCode, $valid = true)
    {
        $this->isValid = $valid;
        $this->errors[] = $errorCode;

        return $this;
    }

    /**
     * @param string $errorCode
     *
     * @return $this
     */
    private function removeError($errorCode)
    {
        $this->errors = array_diff($this->errors, [$errorCode]);

        return $this;
    }

    /**
     * @param $responseClass
     *
     * @return mixed
     */
    private function getErrorResponse($responseClass)
    {
        $data = ['Errors' => implode(',', $this->getErrors())];

        return new $responseClass($data);
    }

    /**
     * @param ResponseInterface $response
     *
     * @throws RequestException
     */
    private function checkResponse($response)
    {
        $hasRequestError = false;
        if (preg_match('/4\d\d/', $response->getStatusCode())) {
            $this->log('error', "Invalid API key or password");
            $this->addError(self::ERROR_HTTP_AUTHENTICATION_ERROR, false);
            $hasRequestError = true;
        } elseif (preg_match('/5\d\d/', $response->getStatusCode())) {
            $this->log('error', "Gateway error - HTTP " . $response->getStatusCode());
            $this->addError(self::ERROR_HTTP_SERVER_ERROR);
            $hasRequestError = true;
        } elseif ($response->getError()) {
            $this->log('error', "Connection error: " . $response->getError());
            $this->addError(self::ERROR_CONNECTION_ERROR);
            $hasRequestError = true;
        }

        if ($hasRequestError) {
            throw new RequestException(sprintf("Last HTTP response status code: %s", $response->getStatusCode()));
        }
    }

    /**
     * Convert a Customer to a Transaction object for a create or update
     * token transaction
     *
     * @param Eway\Rapid\Model\Customer $customer
     * @return Eway\Rapid\Model\Transaction
     */
    private function customerToTransaction($customer)
    {
        $transaction = [
            'Customer' => $customer->toArray(),
            'TransactionType' => TransactionType::MOTO,
        ];

        foreach ($customer->toArray() as $key => $value) {
            if ($key != 'TokenCustomerID') {
                $transaction[$key] = $value;
            }
        }

        /** @var Transaction $transaction */
        return ClassValidator::getInstance('Eway\Rapid\Model\Transaction', $transaction);
    }

    /**
     *
     * @param string $level
     * @param string $message
     */
    private function log($level, $message)
    {
        if (isset($this->logger) && LogLevel::isValidValue($level)) {
            $this->logger->$level($message);
        }
    }

    #endregion
}
