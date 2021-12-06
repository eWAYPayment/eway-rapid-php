<?php

namespace Eway\Rapid\Contract;

use Eway\Rapid\Contract\Http\ResponseInterface;

/**
 * Interface HttpService.
 */
interface HttpService
{
    /*
     * API endpoints
     */

    /**
     * API Transaction Endpoint.
     */
    const API_TRANSACTION = 'Transaction';

    /**
     * API Transaction Endpoint with param Reference (AccessCode or TransactionID).
     */
    const API_TRANSACTION_QUERY = 'Transaction/{Reference}';

    /**
     * API Transaction Invoice Number Endpoint with param InvoiceNumber
     */
    const API_TRANSACTION_INVOICE_NUMBER_QUERY = '/Transaction/InvoiceNumber/{InvoiceNumber}';

    /**
     * API Transaction Invoice Reference Endpoint with param Reference
     */
    const API_TRANSACTION_INVOICE_REFERENCE_QUERY = '/Transaction/InvoiceRef/{InvoiceReference}';

    /**
     * API AccessCode Endpoint.
     */
    const API_ACCESS_CODE = 'AccessCodes';

    /**
     * API AccessCode Endpoint with param AccessCode.
     */
    const API_ACCESS_CODE_QUERY = 'AccessCode/{AccessCode}';

    /**
     * API AccessCodeShared Endpoint.
     */
    const API_ACCESS_CODE_SHARED = 'AccessCodesShared';

    /**
     * API Customer Endpoint with param TokenCustomerID
     */
    const API_CUSTOMER_QUERY = 'Customer/{TokenCustomerID}';

    /**
     * API Transaction Refund Endpoint with param TransactionID
     */
    const API_TRANSACTION_REFUND = 'Transaction/{TransactionID}/Refund';

    /**
     * API Capture Payment Endpoint
     */
    const API_CAPTURE_PAYMENT = 'CapturePayment';

    /**
     * API Cancel Authorisation Endpoint
     */
    const API_CANCEL_AUTHORISATION = 'CancelAuthorisation';

    /**
     * API Settlement Search Endpoint
     */
    const API_SETTLEMENT_SEARCH = 'Search/Settlement';

    /**
     * API 3DS 2.0 Create Enrolment Endpoint
     */
    const API_3DS2_CREATE_ENROLMENT = '3dsenrol';

    /**
     * API 3DS 2.0 Verify Enrolment Endpoint
     */
    const API_3DS2_VERIFY_ENROLMENT = '3dsverify';

    /**
     * cURL hex representation of version 7.30.0
     */
    const CURL_NO_QUIRK_VERSION = 0x071E00;

    /**
     * @param $reference
     *
     * @return ResponseInterface
     */
    public function getTransaction($reference);

    /**
     * @param $invoiceNumber
     *
     * @return ResponseInterface
     */
    public function getTransactionInvoiceNumber($invoiceNumber);

    /**
     * @param $invoiceReference
     *
     * @return ResponseInterface
     */
    public function getTransactionInvoiceReference($invoiceReference);


    /**
     * @param $data
     *
     * @return ResponseInterface
     */
    public function postTransaction($data);


    /**
     * @param $transactionId
     * @param $data
     *
     * @return ResponseInterface
     */
    public function postTransactionRefund($transactionId, $data);


    /**
     * @param $data
     *
     * @return ResponseInterface
     */
    public function postAccessCodeShared($data);


    /**
     * @param $data
     *
     * @return ResponseInterface
     */
    public function post3dsEnrolment($data);


    /**
     * @param $data
     *
     * @return ResponseInterface
     */
    public function post3dsEnrolmentVerification($data);


    /**
     * @param $accessCode
     *
     * @return ResponseInterface
     */
    public function getAccessCode($accessCode);


    /**
     * @param $data
     *
     * @return ResponseInterface
     */
    public function postAccessCode($data);


    /**
     * @param $tokenCustomerId
     *
     * @return ResponseInterface
     */
    public function getCustomer($tokenCustomerId);


    /**
     * @param $data
     *
     * @return ResponseInterface
     */
    public function postCapturePayment($data);


    /**
     * @param $data
     *
     * @return ResponseInterface
     */
    public function postCancelAuthorisation($data);

    /**
     * @param $query
     *
     * @return ResponseInterface
     */
    public function getSettlementSearch($query);

    /**
     * @param string $key
     *
     * @return $this
     */
    public function setKey($key);

    /**
     * @return string
     */
    public function getKey();

    /**
     * @param string $password
     *
     * @return $this
     */
    public function setPassword($password);

    /**
     * @return string
     */
    public function getPassword();

    /**
     * @param string $baseUrl
     *
     * @return $this
     */
    public function setBaseUrl($baseUrl);

    /**
     * @return string
     */
    public function getBaseUrl();

    /**
     * @param int $version
     */
    public function setVersion($version);

    /**
     * @return int
     */
    public function getVersion();
}
