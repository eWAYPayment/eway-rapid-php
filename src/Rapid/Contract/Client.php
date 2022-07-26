<?php

namespace Eway\Rapid\Contract;

use Eway\Rapid\Model\Customer;
use Eway\Rapid\Model\Refund;
use Eway\Rapid\Model\Response\CreateCustomerResponse;
use Eway\Rapid\Model\Response\CreateTransactionResponse;
use Eway\Rapid\Model\Response\QueryCustomerResponse;
use Eway\Rapid\Model\Response\QueryTransactionResponse;
use Eway\Rapid\Model\Response\RefundResponse;
use Eway\Rapid\Model\Transaction;

/**
 * Interface Client.
 */
interface Client
{
    /*
     * SDK options
     */

    /**
     * Rapid SDK Name.
     */
    const NAME = 'eWAY SDK PHP';

    /**
     * Rapid SDK Version.
     */
    const VERSION = '1.4.1';

    /**
     * Sandbox mode.
     */
    const MODE_SANDBOX = 'sandbox';

    /**
     * Production mode.
     */
    const MODE_PRODUCTION = 'production';

    /**
     * Sandbox base URL.
     */
    const ENDPOINT_SANDBOX = 'https://api.sandbox.ewaypayments.com/';

    /**
     * Production base URL.
     */
    const ENDPOINT_PRODUCTION = 'https://api.ewaypayments.com/';

    /*
     * Error Codes
     */

    /**
     * Invalid JSON data in server's response
     */
    const ERROR_INVALID_JSON = 'S9901';

    /**
     * Empty response from server
     */
    const ERROR_EMPTY_RESPONSE = 'S9902';

    /**
     * Invalid endpoint provided
     */
    const ERROR_INVALID_ENDPOINT = 'S9990';

    /**
     * Invalid credential provided
     */
    const ERROR_INVALID_CREDENTIAL = 'S9991';

    /**
     * Failed connecting to eWAY - no response
     */
    const ERROR_CONNECTION_ERROR = 'S9992';

    /**
     * Authentication error - HTTP Status code 4xx returned
     */
    const ERROR_HTTP_AUTHENTICATION_ERROR = 'S9993';

    /**
     * Something wrong with the response. HTTP Status code 5xx returned
     */
    const ERROR_HTTP_SERVER_ERROR = 'S9996';

    /**
     * Invalid argument provided
     */
    const ERROR_INVALID_ARGUMENT = 'S9995';

    /**
     * Is the current client valid or not
     *
     * @return bool
     */
    public function isValid();

    /**
     * Get array of errors
     *
     * @return array
     */
    public function getErrors();

    /**
     * Get the base API URL
     *
     * @return string
     */
    public function getEndpoint();

    /**
     * Call to change the base API URL the Rapid Client is using to communicate with Rapid API
     * Can be MODE_PRODUCTION, MODE_SANDBOX or a URL
     *
     * @param string $endpoint
     *
     * @return $this
     */
    public function setEndpoint($endpoint);

    /**
     * Called to change the credentials the Rapid Client is using to communicate with Rapid API
     *
     * @param string $apiKey
     * @param string $apiPassword
     *
     * @return $this
     */
    public function setCredential($apiKey, $apiPassword);

    /**
     * Sets the version of Rapid API to use (e.g. 40)
     * If not set, the account's default version is used.
     *
     * @param int $version
     *
     * @return $this
     */
    public function setVersion($version);

    /**
     * Sets the PSR-3 compliant logger
     *
     * @param Psr\Log\LoggerInterface $logger
     *
     * @return $this
     */
    public function setLogger($logger);

    /**
     * This Method is used to create a transaction for the merchant in their eWAY account.
     *
     * Depending on the PaymentMethod parameter specified, the transaction may be created immediately,
     * or it may be pending (waiting for Card Details to be supplied via the Responsive Shared Page, or
     * Transparent Redirect).
     *
     * If the 'Capture' flag on the Transaction is true then the funds for the Transaction
     * will be debited immediately from the cardholder (default behaviour).
     * If the 'Capture' flag is false then an Authorisation is created instead.
     * Authorisation is not available for NZ and UK merchants.
     *
     * @param string            $apiMethod
     * @param Transaction|array $transaction
     *
     * @return CreateTransactionResponse
     */
    public function createTransaction($apiMethod, $transaction);

    /**
     * This method is used to determine the status of a transaction.
     * It's primarily of use for PaymentMethods TransparentRedirect and ResponsiveShared,
     * to interrogate Rapid once the transaction is complete.
     *
     * It is also of use in situations where anti-fraud rules have triggered.
     * Once the transaction has been reviewed then the status will change, and
     * in some cases the transaction ID as well.
     *
     * @param string $reference AccessCode or TransactionID
     *
     * @return QueryTransactionResponse
     */
    public function queryTransaction($reference);

    /**
     * This method is used to fetch transaction information once a transaction
     * has been completed using the InvoiceNumber.
     *
     * @param string $invoiceNumber
     *
     * @return QueryTransactionResponse
     */
    public function queryInvoiceNumber($invoiceNumber);

    /**
     * This method is used to fetch transaction information once a transaction has
     * been completed using the InvoiceReference.
     *
     * @param string $invoiceReference
     *
     * @return QueryTransactionResponse
     */
    public function queryInvoiceReference($invoiceReference);

    /**
     * This Method is used to create a token customer for the merchant in their eWAY account.
     * The token customer can be used to create MOTO or Recurring transactions at a later time.
     *
     * Like the CreateTransaction, a PaymentMethod is specified which determines what method
     * will be used to capture the card that will be saved with the customer.
     * Depending on the PaymentMethod the customer may be created immediately, or it may be
     * pending (waiting for Card Details to be supplied by the Responsive Shared Page,
     * or Transparent Redirect).
     *
     * @param string         $apiMethod
     * @param Customer|array $customer
     *
     * @return CreateCustomerResponse
     */
    public function createCustomer($apiMethod, $customer);

    /**
     * This Method is used to update a existing token customer for the merchant in their eWAY account.
     * Card, email and address changes can be made.
     *
     * Like the CreateCustomer, a PaymentMethod is specified which determines what method will be used to capture the
     * card that will be saved with the customer. Depending on the PaymentMethod the customer may be updated
     * immediately, or it may be pending (waiting for Card Details to be supplied by the Responsive Shared Page, or
     * Transparent Redirect).
     *
     * The SDK will use the PaymentMethod parameter to determine what type of transaction to create as per the
     * transaction type mapping table in the Create (Transaction) API Method spec (above).
     *
     * @param string         $apiMethod
     * @param Customer|array $customer
     *
     * @return CreateCustomerResponse
     */
    public function updateCustomer($apiMethod, $customer);

    /**
     * This method is used to return the details of a Token Customer. This includes
     * masked Card information for displaying in a UI to a user.
     *
     * @param string $tokenCustomerId Token Customer ID
     *
     * @return QueryCustomerResponse
     */
    public function queryCustomer($tokenCustomerId);

    /**
     * Refunds all or part of a previous transaction.
     *
     * @param Refund|array $refund Contains the details of the Refund
     *
     * @return RefundResponse
     */
    public function refund($refund);

    /**
     * This is used to cancel a non captured transaction (an Authorisation).
     *
     * @param string|int $transactionId Transaction Id of the transaction to be cancel
     *
     * @return RefundResponse
     */
    public function cancelTransaction($transactionId);

    /**
     * Search settlements
     *
     * @param SettlementSearch|array $query Settlement Search query
     *
     * @return SettlementSearchResponse
     */
    public function settlementSearch($query);

    /**
     * @param $transaction
     * @return mixed
     */
    public function create3dsEnrolment($transaction);

    /**
     * @param $transaction
     * @return mixed
     */
    public function verify3dsEnrolment($transaction);
}
