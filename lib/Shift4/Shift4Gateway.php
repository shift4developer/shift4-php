<?php

namespace Shift4;

use Shift4\Connection\Connection;
use Shift4\Connection\CurlConnection;
use Shift4\Exception\Shift4Exception;
use Shift4\Util\ObjectSerializer;
use Shift4\Util\RequestOptions;

class Shift4Gateway
{
    const VERSION = '4.0.0';
    const DEFAULT_ENDPOINT = 'https://api.shift4.com';
    const DEFAULT_UPLOADS_ENDPOINT = "https://uploads.api.shift4.com/";

    private $objectSerializer;

    /**
     * @var \Shift4\Connection\Connection
     */
    private $connection;

    private $privateKey;

    private $endpoint = self::DEFAULT_ENDPOINT;

    private $uploadsEndpoint = self::DEFAULT_UPLOADS_ENDPOINT;

    private $userAgent;

    public function __construct($privateKey = null, ?Connection $connection = null)
    {
        $this->objectSerializer = new ObjectSerializer();

        $this->privateKey = $privateKey;
        $this->connection = $connection ? $connection : new CurlConnection();
    }

    /**
     * @param \Shift4\Request\ChargeRequest $request
     * @return \Shift4\Response\Charge
     */
    public function createCharge($request, ?RequestOptions $requestOptions = null)
    {
        return $this->post('/charges', $request, '\Shift4\Response\Charge', $requestOptions);
    }

    /**
     * @param \Shift4\Request\CaptureRequest $request
     * @return \Shift4\Response\Charge
     */
    public function captureCharge($request, ?RequestOptions $requestOptions = null)
    {
        return $this->post('/charges/{chargeId}/capture', $request, '\Shift4\Response\Charge', $requestOptions);
    }

    /**
     * @param string $chargeId
     * @return \Shift4\Response\Charge
     */
    public function retrieveCharge($chargeId)
    {
        return $this->get("/charges/{$chargeId}", '\Shift4\Response\Charge');
    }

    /**
     * @param \Shift4\Request\ChargeUpdateRequest $request
     * @return \Shift4\Response\Charge
     */
    public function updateCharge($request, ?RequestOptions $requestOptions = null)
    {
        return $this->post('/charges/{chargeId}', $request, '\Shift4\Response\Charge', $requestOptions);
    }

    /**
     * @param \Shift4\Request\RefundRequest $request
     * @return \Shift4\Response\Refund
     *
     * @deprecated For backward compatibility only. Use "createRefund($request)".
     */
    public function refundCharge($request, ?RequestOptions $requestOptions = null)
    {
        return $this->createRefund($request, $requestOptions);
    }

    /**
     * @param \Shift4\Request\ChargeListRequest $request
     * @return \Shift4\Response\ListResponse
     */
    public function listCharges($request = null)
    {
        return $this->getList('/charges', $request, '\Shift4\Response\Charge');
    }

    /**
     * @param \Shift4\Request\CustomerRequest $request
     * @return \Shift4\Response\Customer
     */
    public function createCustomer($request, ?RequestOptions $requestOptions = null)
    {
        return $this->post('/customers', $request, '\Shift4\Response\Customer', $requestOptions);
    }

    /**
     * @param string $customerId
     * @return \Shift4\Response\Customer
     */
    public function retrieveCustomer($customerId)
    {
        return $this->get("/customers/{$customerId}", '\Shift4\Response\Customer');
    }

    /**
     * @param \Shift4\Request\CustomerUpdateRequest $request
     * @return \Shift4\Response\Customer
     */
    public function updateCustomer($request, ?RequestOptions $requestOptions = null)
    {
        return $this->post('/customers/{customerId}', $request, '\Shift4\Response\Customer', $requestOptions);
    }

    /**
     * @param string $customerId
     * @return \Shift4\Response\DeleteResponse
     */
    public function deleteCustomer($customerId)
    {
        return $this->delete("/customers/{$customerId}", null, '\Shift4\Response\DeleteResponse');
    }

    /**
     * @param \Shift4\Request\CustomerListRequest $request
     * @return \Shift4\Response\ListResponse
     */
    public function listCustomers($request = null)
    {
        return $this->getList('/customers', $request, '\Shift4\Response\Customer');
    }

    /**
     * @param \Shift4\Request\CardRequest $request
     * @return \Shift4\Response\Card
     */
    public function createCard($request, ?RequestOptions $requestOptions = null)
    {
        return $this->post('/customers/{customerId}/cards', $request, '\Shift4\Response\Card', $requestOptions);
    }

    /**
     * @param string $customerId
     * @param string $cardId
     * @return \Shift4\Response\Card
     */
    public function retrieveCard($customerId, $cardId)
    {
        return $this->get("/customers/{$customerId}/cards/{$cardId}", '\Shift4\Response\Card');
    }

    /**
     * @param \Shift4\Request\CardUpdateRequest $request
     * @return \Shift4\Response\Card
     */
    public function updateCard($request, ?RequestOptions $requestOptions = null)
    {
        return $this->post('/customers/{customerId}/cards/{cardId}', $request, '\Shift4\Response\Card', $requestOptions);
    }

    /**
     * @param string $customerId
     * @param string $cardId
     * @return \Shift4\Response\DeleteResponse
     */
    public function deleteCard($customerId, $cardId)
    {
        return $this->delete("/customers/{$customerId}/cards/{$cardId}", null, '\Shift4\Response\DeleteResponse');
    }

    /**
     * @param \Shift4\Request\CardListRequest $request
     * @return \Shift4\Response\ListResponse
     */
    public function listCards($request)
    {
        return $this->getList('/customers/{customerId}/cards', $request, '\Shift4\Response\Card');
    }

    /**
     * @param string $paymentMethodId
     * @return \Shift4\Response\PaymentMethod
     */
    public function retrievePaymentMethod($paymentMethodId)
    {
        return $this->get("/payment-methods/{$paymentMethodId}", '\Shift4\Response\PaymentMethod');
    }

    /**
     * @param \Shift4\Request\PaymentMethodRequest $request
     * @return \Shift4\Response\PaymentMethod
     */
    public function createPaymentMethod($request, ?RequestOptions $requestOptions = null)
    {
        return $this->post("/payment-methods", $request, '\Shift4\Response\PaymentMethod', $requestOptions);
    }

    /**
     * @param string $paymentMethodId
     * @return \Shift4\Response\DeleteResponse
     */
    public function deletePaymentMethod($paymentMethodId)
    {
        return $this->delete("/payment-methods/{$paymentMethodId}", null, '\Shift4\Response\DeleteResponse');
    }

    /**
     * @param \Shift4\Request\PaymentMethodListRequest $request
     * @return \Shift4\Response\ListResponse
     */
    public function listPaymentMethods($request)
    {
        return $this->getList('/payment-methods', $request, '\Shift4\Response\PaymentMethod');
    }

    /**
     * @param \Shift4\Request\SubscriptionRequest $request
     * @return \Shift4\Response\Subscription
     */
    public function createSubscription($request, ?RequestOptions $requestOptions = null)
    {
        return $this->post('/subscriptions', $request, '\Shift4\Response\Subscription', $requestOptions);
    }

    /**
     * @param string $subscriptionId
     * @return \Shift4\Response\Subscription
     */
    public function retrieveSubscription($subscriptionId)
    {
        return $this->get("/subscriptions/{$subscriptionId}", '\Shift4\Response\Subscription');
    }

    /**
     * @param \Shift4\Request\SubscriptionUpdateRequest $request
     * @return \Shift4\Response\Subscription
     */
    public function updateSubscription($request, ?RequestOptions $requestOptions = null)
    {
        return $this->post('/subscriptions/{subscriptionId}', $request, '\Shift4\Response\Subscription', $requestOptions);
    }

    /**
     * @param \Shift4\Request\SubscriptionCancelRequest $request
     * @return \Shift4\Response\Subscription
     */
    public function cancelSubscription($request)
    {
        return $this->delete('/subscriptions/{subscriptionId}', $request, '\Shift4\Response\Subscription');
    }

    /**
     * @param \Shift4\Request\SubscriptionListRequest $request
     * @return \Shift4\Response\ListResponse
     */
    public function listSubscriptions($request)
    {
        return $this->getList('/subscriptions', $request, '\Shift4\Response\Subscription');
    }

    /**
     * @param \Shift4\Request\PlanRequest $request
     * @return \Shift4\Response\Plan
     */
    public function createPlan($request, ?RequestOptions $requestOptions = null)
    {
        return $this->post('/plans', $request, '\Shift4\Response\Plan', $requestOptions);
    }

    /**
     * @param string $planId
     * @return \Shift4\Response\Plan
     */
    public function retrievePlan($planId)
    {
        return $this->get("/plans/{$planId}", '\Shift4\Response\Plan');
    }

    /**
     * @param \Shift4\Request\PlanUpdateRequest $request
     * @return \Shift4\Response\Plan
     */
    public function updatePlan($request, ?RequestOptions $requestOptions = null)
    {
        return $this->post('/plans/{planId}', $request, '\Shift4\Response\Plan', $requestOptions);
    }

    /**
     * @param string $planId
     * @return \Shift4\Response\DeleteResponse
     */
    public function deletePlan($planId)
    {
        return $this->delete("/plans/{$planId}", null, '\Shift4\Response\DeleteResponse');
    }

    /**
     * @param \Shift4\Request\PlanListRequest $request
     * @return \Shift4\Response\ListResponse
     */
    public function listPlans($request = null)
    {
        return $this->getList('/plans', $request, '\Shift4\Response\Plan');
    }

    /**
     * @param string $eventId
     * @return \Shift4\Response\Event
     */
    public function retrieveEvent($eventId)
    {
        return $this->get("/events/{$eventId}", '\Shift4\Response\Event');
    }

    /**
     * @param \Shift4\Request\EventListRequest $request
     * @return \Shift4\Response\ListResponse
     */
    public function listEvents($request = null)
    {
        return $this->getList('/events', $request, '\Shift4\Response\Event');
    }

    /**
     * @param \Shift4\Request\TokenRequest $request
     * @return \Shift4\Response\Token
     */
    public function createToken($request)
    {
        return $this->post('/tokens', $request, '\Shift4\Response\Token');
    }

    /**
     * @param string $tokenId
     * @return \Shift4\Response\Token
     */
    public function retrieveToken($tokenId)
    {
        return $this->get("/tokens/{$tokenId}", '\Shift4\Response\Token');
    }

    /**
     * @param \Shift4\Request\BlacklistRuleRequest $request
     * @return \Shift4\Response\BlacklistRule
     */
    public function createBlacklistRule($request, ?RequestOptions $requestOptions = null)
    {
        return $this->post('/blacklist', $request, '\Shift4\Response\BlacklistRule', $requestOptions);
    }

    /**
     * @param string $blacklistRuleId
     * @return \Shift4\Response\BlacklistRule
     */
    public function retrieveBlacklistRule($blacklistRuleId)
    {
        return $this->get("/blacklist/{$blacklistRuleId}", '\Shift4\Response\BlacklistRule');
    }

    /**
     * @param string $blacklistRuleId
     * @return \Shift4\Response\DeleteResponse
     */
    public function deleteBlacklistRule($blacklistRuleId)
    {
        return $this->delete("/blacklist/{$blacklistRuleId}", null, '\Shift4\Response\DeleteResponse');
    }

    /**
     * @param \Shift4\Request\BlacklistRuleListRequest $request
     * @return \Shift4\Response\ListResponse
     */
    public function listBlacklistRules($request = null)
    {
        return $this->getList('/blacklist', $request, '\Shift4\Response\BlacklistRule');
    }

    /**
     * @param \Shift4\Request\CreditRequest $request
     * @return \Shift4\Response\Credit
     */
    public function createCredit($request, ?RequestOptions $requestOptions = null)
    {
        return $this->post('/credits', $request, '\Shift4\Response\Credit', $requestOptions);
    }

    /**
     * @param string creditId
     * @return \Shift4\Response\Credit
     */
    public function retrieveCredit($creditId)
    {
        return $this->get("/credits/{$creditId}", '\Shift4\Response\Credit');
    }

    /**
     * @param \Shift4\Request\CreditUpdateRequest $request
     * @return \Shift4\Response\Credit
     */
    public function updateCredit($request, ?RequestOptions $requestOptions = null)
    {
        return $this->post('/credits/{creditId}', $request, '\Shift4\Response\Credit', $requestOptions);
    }

    /**
     * @param \Shift4\Request\CreditListRequest $request
     * @return \Shift4\Response\ListResponse
     */
    public function listCredits($request = null)
    {
        return $this->getList('/credits', $request, '\Shift4\Response\Credit');
    }

    /**
     * @param string $file
     * @param string $purpose
     * @return \Shift4\Response\FileUpload
     */
    public function createFileUpload($file, $purpose)
    {
        $files = ['file' => $file];
        $form = ['purpose' => $purpose];

        return $this->multipart('/files', $files, $form, '\Shift4\Response\FileUpload');
    }

    /**
     * @param string $fileUploadId
     * @return \Shift4\Response\FileUpload
     */
    public function retrieveFileUpload($fileUploadId)
    {
        return $this->getFromEndpoint($this->uploadsEndpoint, "/files/{$fileUploadId}", '\Shift4\Response\FileUpload');
    }

    /**
     * @param \Shift4\Request\FileUploadListRequest $request
     * @return \Shift4\Response\ListResponse
     */
    public function listFileUploads($request = null)
    {
        return $this->listFromEndpoint($this->uploadsEndpoint, '/files', $request, '\Shift4\Response\FileUpload');
    }

    /**
     * @param string $disputeId
     * @return \Shift4\Response\Dispute
     */
    public function retrieveDispute($disputeId)
    {
        return $this->get("/disputes/{$disputeId}", '\Shift4\Response\Dispute');
    }

    /**
     * @param \Shift4\Request\DisputeUpdateRequest $request
     * @return \Shift4\Response\Dispute
     */
    public function updateDispute($request, ?RequestOptions $requestOptions = null)
    {
        return $this->post('/disputes/{disputeId}', $request, '\Shift4\Response\Dispute', $requestOptions);
    }

    /**
     * @param string $disputeId
     * @return \Shift4\Response\Dispute
     */
    public function closeDispute($disputeId, ?RequestOptions $requestOptions = null)
    {
        return $this->post("/disputes/{$disputeId}/close", null, '\Shift4\Response\Dispute', $requestOptions);
    }

    /**
     * @param \Shift4\Request\DisputeListRequest $request
     * @return \Shift4\Response\ListResponse
     */
    public function listDisputes($request = null)
    {
        return $this->getList('/disputes', $request, '\Shift4\Response\Dispute');
    }

    /**
     * @param string $fraudWarningId
     * @return \Shift4\Response\Dispute
     */
    public function retrieveFraudWarning($fraudWarningId)
    {
        return $this->get("/fraud-warnings/{$fraudWarningId}", '\Shift4\Response\FraudWarning');
    }

    /**
     * @param \Shift4\Request\FraudWarningListRequest $request
     * @return \Shift4\Response\ListResponse
     */
    public function listFraudWarnings($request = null)
    {
        return $this->getList('/fraud-warnings', $request, '\Shift4\Response\FraudWarning');
    }

    /**
     * @param string $refundId
     * @return \Shift4\Response\Refund
     */
    public function retrieveRefund($refundId)
    {
        return $this->get("/refunds/{$refundId}", '\Shift4\Response\Refund');
    }

    /**
     * @param \Shift4\Request\RefundRequest $request
     * @return \Shift4\Response\Refund
     */
    public function createRefund($request, ?RequestOptions $requestOptions = null)
    {
        return $this->post('/refunds', $request, '\Shift4\Response\Refund', $requestOptions);
    }

    /**
     * @param \Shift4\Request\RefundListRequest $request
     * @return \Shift4\Response\ListResponse
     */
    public function listRefunds($request)
    {
        return $this->getList('/refunds', $request, '\Shift4\Response\Refund');
    }

    /**
     * @param string $payoutId
     * @return \Shift4\Response\Payout
     */
    public function retrievePayout($payoutId)
    {
        return $this->get("/payouts/{$payoutId}", '\Shift4\Response\Payout');
    }

    /**
     * @return \Shift4\Response\Payout
     */
    public function createPayout($requestOptions = null)
    {
        return $this->post('/payouts', null, '\Shift4\Response\Payout', $requestOptions);
    }

    /**
     * @param \Shift4\Request\PayoutListRequest $request
     * @return \Shift4\Response\ListResponse
     */
    public function listPayouts($request = null)
    {
        return $this->getList('/payouts', $request, '\Shift4\Response\Payout');
    }

    /**
     * @param \Shift4\Request\PayoutTransactionListRequest $request
     * @return \Shift4\Response\ListResponse
     */
    public function listPayoutTransactions($request)
    {
        return $this->getList('/payout-transactions', $request, '\Shift4\Response\PayoutTransaction');
    }

    /**
     * @param \Shift4\Request\CheckoutRequest $request
     * @return string
     */
    public function signCheckoutRequest($request)
    {
        $path = '';
        $data = $this->objectSerializer->serialize($request, $path);

        $signature = hash_hmac('sha256', $data, $this->privateKey);

        return base64_encode($signature . "|" . $data);
    }

    private function get($path, $responseClass)
    {
        return $this->getFromEndpoint($this->endpoint, $path, $responseClass);
    }

    private function getFromEndpoint($endpoint, $path, $responseClass)
    {
        $response = $this->connection->get($endpoint . $path, $this->buildHeaders());
        $this->ensureSuccess($response);
        return $this->objectSerializer->deserialize($response['body'], $responseClass);
    }

    private function post($path, $request, $responseClass, ?RequestOptions $requestOptions = null)
    {
        $requestBody = $this->objectSerializer->serialize($request, $path);
        $response = $this->connection->post($this->endpoint . $path, $requestBody, $this->buildHeaders($requestOptions));
        $this->ensureSuccess($response);
        return $this->objectSerializer->deserialize($response['body'], $responseClass);
    }

    private function multipart($path, $files, $form, $responseClass)
    {
        $response = $this->connection->multipart($this->uploadsEndpoint . $path, $files, $form, $this->buildHeaders());
        $this->ensureSuccess($response);
        return $this->objectSerializer->deserialize($response['body'], $responseClass);
    }

    private function getList($path, $request, $elementClass)
    {
        return $this->listFromEndpoint($this->endpoint, $path, $request, $elementClass);
    }

    private function listFromEndpoint($endpoint, $path, $request, $elementClass)
    {
        $url = $this->buildQueryString($endpoint . $path, $request);
        $response = $this->connection->get($url, $this->buildHeaders());
        $this->ensureSuccess($response);
        return $this->objectSerializer->deserializeList($response['body'], $elementClass);
    }

    private function delete($path, $request, $responseClass)
    {
        $url = $this->endpoint . $this->buildQueryString($path, $request);
        $response = $this->connection->delete($url, $this->buildHeaders());
        $this->ensureSuccess($response);
        return $this->objectSerializer->deserialize($response['body'], $responseClass);
    }

    private function ensureSuccess($response)
    {
        if ($response['status'] != 200) {
            $error = $this->objectSerializer->deserialize($response['body'], '\Shift4\Response\ErrorResponse');
            throw new Shift4Exception($error);
        }
        return $response;
    }

    private function buildQueryString($path, $request)
    {
        if ($request == null) {
            return $path;
        }

        $queryString = $this->objectSerializer->serializeToQueryString($request, $path);
        return $path . $queryString;
    }

    private function buildHeaders(?RequestOptions $requestOptions = null)
    {
        $headers = [
            'Authorization' => 'Basic ' . base64_encode($this->privateKey . ':'),
            'Content-Type'  => 'application/json',
            'User-Agent'    => ($this->userAgent ? $this->userAgent . ' ' : '') . 'Shift4-PHP/' . self::VERSION . ' (PHP/' . phpversion() . ')'
        ];

        if ($requestOptions !== null && $requestOptions->hasIdempotencyKey())
        {
            $headers['Idempotency-Key'] = $requestOptions->getIdempotencyKey();
        }

        return $headers;
    }

    public function setPrivateKey($privateKey)
    {
        $this->privateKey = $privateKey;
    }

    public function setConnection(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function setEndpoint($endpoint)
    {
        $this->endpoint = $endpoint;
    }

    public function setUploadsEndpoint($uploadsEndpoint)
    {
        $this->uploadsEndpoint = $uploadsEndpoint;
    }

    public function setUserAgent($userAgent)
    {
        $this->userAgent = $userAgent;
    }
}
