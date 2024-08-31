<?php
require_once 'bootstrap.php';

use Shift4\Request\CaptureRequest;
use Shift4\Request\ChargeListRequest;
use Shift4\Request\ChargeUpdateRequest;
use Shift4\Request\CreatedFilter;
use Shift4\Util\RequestOptions;

class ChargeTest extends AbstractGatewayTestBase
{

    function testCreateCharge()
    {
        // given
        $request = Data::chargeRequest();

        // when
        $charge = $this->gateway->createCharge($request);

        // then
        Assert::assertCharge($request, $charge);
    }

    function testCaptureCharge()
    {
        // given
        $charge = $this->gateway->createCharge(Data::chargeRequest()->captured(false));
        $request = (new CaptureRequest())->chargeId($charge->getId());

        // when
        $charge = $this->gateway->captureCharge($request);

        // then
        self::assertEquals(true, $charge->getCaptured());
    }

    function testRetrieveCharge()
    {
        // given
        $request = Data::chargeRequest();
        $charge = $this->gateway->createCharge($request);

        // when
        $charge = $this->gateway->retrieveCharge($charge->getId());

        // then
        Assert::assertCharge($request, $charge);
    }

    function testUpdateCharge()
    {
        // given
        $customer = $this->gateway->createCustomer(Data::customerRequest());

        $request = Data::chargeRequest();
        $charge = $this->gateway->createCharge($request);

        $updateRequest = (new ChargeUpdateRequest())
            ->chargeId($charge->getId())
            ->description('updated-description')
            ->customerId($customer->getId())
            ->metadata(array('updated-key' => 'updated-value'));

        // when
        $charge = $this->gateway->updateCharge($updateRequest);

        // then
        $request->description($updateRequest->getDescription());
        $request->metadata($updateRequest->getMetadata());
        $request->customerId($customer->getId());

        Assert::assertCharge($request, $charge);
    }

    function testListCharges()
    {
        // given
        $customer = $this->gateway->createCustomer(Data::customerRequest());

        $chargeRequest = Data::chargeRequest()->customerId($customer->getId());
        $charge = $this->gateway->createCharge($chargeRequest);
        $this->gateway->createCharge($chargeRequest);
        $this->gateway->createCharge($chargeRequest);

        $listRequest = (new ChargeListRequest())
            ->limit(2)
            ->includeTotalCount(true)
            ->customerId($customer->getId())
            ->created((new CreatedFilter())->gte($charge->getCreated()));

        // when
        $list = $this->gateway->listCharges($listRequest);

        // then
        self::assertEquals(3, $list->getTotalCount());
        self::assertEquals(2, count($list->getList()));
        foreach ($list->getList() as $charge) {
            Assert::assertCharge($chargeRequest, $charge);
        }
    }

    function testChargeRequestViaArray()
    {
        // given
        $request = array(
            'amount' => 499,
            'currency' => 'EUR',
            'card' => array(
                'number' => '4242424242424242',
                'expMonth' => '11',
                'expYear' => strval(date('Y') + 1),
                'cvc' => '123'
            ),
            'metadata' => array()
        );

        // when
        $charge = $this->gateway->createCharge($request)->toArray();

        // then
        self::assertNotNull($charge['id']);
        self::assertNotNull($charge['created']);

        self::assertEquals($request['amount'], $charge['amount']);
        self::assertEquals($request['currency'], $charge['currency']);

        self::assertNotNull($charge['card']['id']);
        self::assertNotNull($charge['card']['created']);

        self::assertEquals(substr($request['card']['number'], 0, 6), $charge['card']['first6']);
        self::assertEquals(substr($request['card']['number'], -4, 4), $charge['card']['last4']);
        self::assertMatchesRegularExpression('/\w+/', $charge['card']['fingerprint']);
        self::assertMatchesRegularExpression('/\w+/', $charge['card']['type']);
        self::assertMatchesRegularExpression('/\w+/', $charge['card']['brand']);
        self::assertEquals($request['card']['expMonth'], $charge['card']['expMonth']);
        self::assertEquals($request['card']['expYear'], $charge['card']['expYear']);
    }

    function testCreateChargeWithAddress()
    {
        // given
        $request = Data::chargeRequest()
            ->shipping(Data::shippingRequest())
            ->billing(Data::billingRequest());

        // when
        $charge = $this->gateway->createCharge($request);

        // then
        Assert::assertCharge($request, $charge);
    }

    function testUpdateChargeWithAddress()
    {
        // given
        $request = Data::chargeRequest();
        $charge = $this->gateway->createCharge($request);

        $updateRequest = (new ChargeUpdateRequest())
            ->chargeId($charge->getId())
            ->shipping(Data::shippingRequest())
            ->billing(Data::billingRequest());

        // when
        $charge = $this->gateway->updateCharge($updateRequest);

        // then
        $request->shipping($updateRequest->getShipping());
        $request->billing($updateRequest->getBilling());

        Assert::assertCharge($request, $charge);
    }

    function testDispute()
    {
        // given
        $request = Data::chargeRequest();

        $request->getCard()->number('4242000000000018');
        $charge = $this->gateway->createCharge($request);

        // when
        $this->waitForChargeback($charge);

        // then
        $charge = $this->gateway->retrieveCharge($charge->getId());

        self::assertNotNull($charge->getDispute());
        self::assertNull($charge->getDispute()->getCharge());

        self::assertNotNull($charge->getDispute()->getCreated());
        self::assertNotNull($charge->getDispute()->getUpdated());
        self::assertEquals($request->getAmount(), $charge->getDispute()->getAmount());
        self::assertEquals($request->getCurrency(), $charge->getDispute()->getCurrency());
        self::assertEquals('CHARGEBACK_NEW', $charge->getDispute()->getStatus());
        self::assertEquals('GENERAL', $charge->getDispute()->getReason());
        self::assertEquals(false, $charge->getDispute()->getAcceptedAsLost());
    }

    public function testCreateChargeForGooglePayPanOnly()
    {
        // given
        $request = Data::googlePayPaymentMethodPanOnly();
        $response = $this->gateway->createPaymentMethod($request);

        // then
        Assert::assertEquals($request->getType(), $response->getType());
        Assert::assertNotNull($response->getGooglePay());
        Assert::assertNotNull($response->getFlow());
        Assert::assertNotNull($response->getFlow()->getNextAction());
    }

    public function testCreateChargeForGoogle3ds()
    {
        // given
        $source = $this->gateway->createPaymentMethod(Data::googlePayPaymentMethod3ds());
        $request = Data::threeDSecurePaymentMethod($source, 'USD', 400);
        $response = $this->gateway->createPaymentMethod($request);

        // then
        Assert::assertEquals('three_d_secure', $response->getType());
        Assert::assertNotNull($response->getThreeDSecure());
        Assert::assertEquals($request->getThreeDSecure()->getAmount(), $response->getThreeDSecure()->getAmount());
        Assert::assertEquals($request->getThreeDSecure()->getCurrency(), $response->getThreeDSecure()->getCurrency());
        Assert::assertEquals($request->getSource(), $response->getSource()['id']);
        Assert::assertNotNull($response->getFlow());
        Assert::assertNotNull($response->getFlow()->getNextAction());
    }

    function testWillNotCreateDuplicateIfSameIdempotencyKeyIsUsed()
    {
        // given
        $request = Data::chargeRequest();
        $requestOptions = new RequestOptions();
        $requestOptions->idempotencyKey(uniqid());

        // when
        $first_call_response = $this->gateway->createCharge($request, $requestOptions);
        $second_call_response = $this->gateway->createCharge($request, $requestOptions);

        // then
        Assert::assertEquals($first_call_response->getId(), $second_call_response->getId());
    }

    function testWillCreateTwoInstancesIfDifferentIdempotencyKeysAreUsed()
    {
        // given
        $request = Data::chargeRequest();
        $requestOptions = new RequestOptions();
        $requestOptions->idempotencyKey(uniqid());
        $otherRequestOptions = new RequestOptions();
        $otherRequestOptions->idempotencyKey(uniqid());

        // when
        $first_call_response = $this->gateway->createCharge($request, $requestOptions);
        $second_call_response = $this->gateway->createCharge($request, $otherRequestOptions);

        // then
        Assert::assertNotEquals($first_call_response->getId(), $second_call_response->getId());
    }

    function testWillCreateTwoInstancesIfNoIdempotencyKeysAreUsed()
    {
        // given
        $request = Data::chargeRequest();

        // when
        $first_call_response = $this->gateway->createCharge($request);
        $second_call_response = $this->gateway->createCharge($request);

        // then
        Assert::assertNotEquals($first_call_response->getId(), $second_call_response->getId());
    }

    function testWillThrowExceptionIfSameIdempotencyKeyIsUsedForTwoDifferentUpdateRequests()
    {
        // given
        $requestOptions = new RequestOptions();
        $requestOptions->idempotencyKey(uniqid());
        $request = Data::chargeRequest();
        $charge = $this->gateway->createCharge($request);

        $updateRequest = (new ChargeUpdateRequest())
            ->chargeId($charge->getId())
            ->description('updated-description');

        // when
        $this->gateway->updateCharge($updateRequest, $requestOptions);
        $updateRequest->description("other-description");
        $exception = Assert::catchShift4Exception(function () use ($updateRequest, $requestOptions) {
           $this->gateway->updateCharge($updateRequest, $requestOptions);
        });

        // then
        Assert::assertSame('Idempotent key used for request with different parameters.', $exception->getMessage());
    }
}
