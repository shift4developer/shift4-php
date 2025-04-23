<?php
require_once 'bootstrap.php';

use Shift4\Request\SubscriptionUpdateRequest;
use Shift4\Request\SubscriptionCancelRequest;
use Shift4\Request\SubscriptionListRequest;
use Shift4\Request\CreatedFilter;
use Shift4\Util\RequestOptions;

class SubscriptionTest extends AbstractGatewayTestBase
{
    function testCreateSubscription()
    {
        // given
        $customer = $this->gateway->createCustomer(Data::customerRequest());
        $plan = $this->gateway->createPlan(Data::planRequest());
        
        $request = Data::subscriptionRequest($customer, $plan)->card(Data::cardRequest());
        
        // when
        $subscription = $this->gateway->createSubscription($request);
        
        // then
        Assert::assertSubscription($request, $subscription);
    }
    
    function testRetrieveSubscription()
    {
        // given
        $customer = $this->gateway->createCustomer(Data::customerRequest());
        $plan = $this->gateway->createPlan(Data::planRequest());
        
        $request = Data::subscriptionRequest($customer, $plan)->card(Data::cardRequest());
        $subscription = $this->gateway->createSubscription($request);
        
        // when
        $subscription = $this->gateway->retrieveSubscription($subscription->getId());
        
        // then
        Assert::assertSubscription($request, $subscription);
    }
    
    function testUpdateSubscription()
    {
        // given
        $customer = $this->gateway->createCustomer(Data::customerWithCardRequest());
        $plan = $this->gateway->createPlan(Data::planRequest());
        
        $request = Data::subscriptionRequest($customer, $plan);
        $subscription = $this->gateway->createSubscription($request);

        $newPlan = $this->gateway->createPlan(Data::planRequest());

        $updateRequest = (new SubscriptionUpdateRequest())
            ->customerId($subscription->getCustomerId())
            ->subscriptionId($subscription->getId())
            ->planId($newPlan->getId())
            ->card(Data::cardRequest())
            ->quantity(3)
            ->captureCharges(true)
            ->currentPeriodEnd(time() + 15 * 24 * 60 * 60)
            ->metadata(array('updated-key' => 'updated-value'));
        
        // when
        $subscription = $this->gateway->updateSubscription($updateRequest);
        
        // then
        $request->planId($updateRequest->getPlanId());
        $request->card($updateRequest->getCard());
        $request->quantity($updateRequest->getQuantity());
        $request->captureCharges($updateRequest->getCaptureCharges());
        $request->trialEnd($updateRequest->getCurrentPeriodEnd());
        $request->metadata($updateRequest->getMetadata());
        
        Assert::assertSubscription($request, $subscription);
    }

    function testCancelSubscription()
    {
        // given
        $customer = $this->gateway->createCustomer(Data::customerWithCardRequest());
        $plan = $this->gateway->createPlan(Data::planRequest());
        
        $subscription = $this->gateway->createSubscription(Data::subscriptionRequest($customer, $plan));
        
        $request = (new SubscriptionCancelRequest())
            ->customerId($subscription->getCustomerId())
            ->subscriptionId($subscription->getId())
            ->atPeriodEnd(false);
        
        // when
        $subscription = $this->gateway->cancelSubscription($request);
        
        // then
        Assert::assertTrue($subscription->getDeleted());
    }
    
    function testListSubscriptions()
    {
        // given
        $customer = $this->gateway->createCustomer(Data::customerWithCardRequest());
        $plan = $this->gateway->createPlan(Data::planRequest());
        
        $request = Data::subscriptionRequest($customer, $plan);
        
        $subscription = $this->gateway->createSubscription($request);
        $this->gateway->createSubscription($request);
        $this->gateway->createSubscription($request);
        
        $listRequest = (new SubscriptionListRequest())
            ->customerId($customer->getId())
            ->limit(2)
            ->includeTotalCount(true)
            ->created((new CreatedFilter())->gte($subscription->getCreated()));
        
        // when
        $list = $this->gateway->listSubscriptions($listRequest);
        
        // then
        self::assertEquals(3, $list->getTotalCount());
        self::assertEquals(2, count($list->getList()));
        foreach ($list->getList() as $subscription) {
            Assert::assertSubscription($request, $subscription);
        }
    }
    
    function testCreateSubscriptionWithAddress()
    {
        // given
        $customer = $this->gateway->createCustomer(Data::customerRequest());
        $plan = $this->gateway->createPlan(Data::planRequest());
        
        $request = Data::subscriptionRequest($customer, $plan)
            ->card(Data::cardRequest())
            ->shipping(Data::shippingRequest())
            ->billing(Data::billingRequest());
        
        // when
        $subscription = $this->gateway->createSubscription($request);
        
        // then
        Assert::assertSubscription($request, $subscription);
    }
    
    function testUpdateSubscriptionWithAddress()
    {
        // given
        $customer = $this->gateway->createCustomer(Data::customerWithCardRequest());
        $plan = $this->gateway->createPlan(Data::planRequest());
    
        $request = Data::subscriptionRequest($customer, $plan);
        $subscription = $this->gateway->createSubscription($request);
    
        $updateRequest = (new SubscriptionUpdateRequest())
            ->customerId($subscription->getCustomerId())
            ->subscriptionId($subscription->getId())
            ->shipping(Data::shippingRequest())
            ->billing(Data::billingRequest());
    
        // when
        $subscription = $this->gateway->updateSubscription($updateRequest);
    
        // then
        $request->shipping($updateRequest->getShipping());
        $request->billing($updateRequest->getBilling());
    
        Assert::assertSubscription($request, $subscription);
    }


    function testWillNotCreateDuplicateIfSameIdempotencyKeyIsUsed()
    {
        // given
        $customer = $this->gateway->createCustomer(Data::customerRequest());
        $plan = $this->gateway->createPlan(Data::planRequest());

        $request = Data::subscriptionRequest($customer, $plan)->card(Data::cardRequest());
        $requestOptions = new RequestOptions();
        $requestOptions->idempotencyKey(uniqid());

        // when
        $first_call_response = $this->gateway->createSubscription($request, $requestOptions);
        $second_call_response = $this->gateway->createSubscription($request, $requestOptions);

        // then
        Assert::assertEquals($first_call_response->getId(), $second_call_response->getId());
    }

    function testWillCreateTwoInstancesIfDifferentIdempotencyKeysAreUsed()
    {
        // given
        $customer = $this->gateway->createCustomer(Data::customerRequest());
        $plan = $this->gateway->createPlan(Data::planRequest());

        $request = Data::subscriptionRequest($customer, $plan)->card(Data::cardRequest());
        $requestOptions = new RequestOptions();
        $requestOptions->idempotencyKey(uniqid());
        $otherRequestOptions = new RequestOptions();
        $otherRequestOptions->idempotencyKey(uniqid());

        // when
        $first_call_response = $this->gateway->createSubscription($request, $requestOptions);
        $second_call_response = $this->gateway->createSubscription($request, $otherRequestOptions);

        // then
        Assert::assertNotEquals($first_call_response->getId(), $second_call_response->getId());
    }

    function testWillCreateTwoInstancesIfNoIdempotencyKeysAreUsed()
    {
        // given
        $customer = $this->gateway->createCustomer(Data::customerRequest());
        $plan = $this->gateway->createPlan(Data::planRequest());

        $request = Data::subscriptionRequest($customer, $plan)->card(Data::cardRequest());

        // when
        $first_call_response = $this->gateway->createSubscription($request);
        $second_call_response = $this->gateway->createSubscription($request);

        // then
        Assert::assertNotEquals($first_call_response->getId(), $second_call_response->getId());
    }

    function testWillThrowExceptionIfSameIdempotencyKeyIsUsedForTwoDifferentCreateRequests()
    {
        // given
        $customer = $this->gateway->createCustomer(Data::customerRequest());
        $plan = $this->gateway->createPlan(Data::planRequest());

        $request = Data::subscriptionRequest($customer, $plan)->card(Data::cardRequest());
        $requestOptions = new RequestOptions();
        $requestOptions->idempotencyKey(uniqid());

        // when
        $this->gateway->createSubscription($request, $requestOptions);
        $request->planId(42);

        $exception = Assert::catchShift4Exception(function () use ($request, $requestOptions) {
            $this->gateway->createSubscription($request, $requestOptions);
        });

        // then
        Assert::assertSame('Idempotent key used for request with different parameters.', $exception->getMessage());
    }

    function testWillThrowExceptionIfSameIdempotencyKeyIsUsedForTwoDifferentUpdateRequests() {
        // given
        $customer = $this->gateway->createCustomer(Data::customerRequest());
        $plan = $this->gateway->createPlan(Data::planRequest());

        $request = Data::subscriptionRequest($customer, $plan)->card(Data::cardRequest());
        $subscription = $this->gateway->createSubscription($request);

        $updateRequest = (new SubscriptionUpdateRequest())
            ->customerId($subscription->getCustomerId())
            ->subscriptionId($subscription->getId())
            ->shipping(Data::shippingRequest())
            ->billing(Data::billingRequest());

        $requestOptions = new RequestOptions();
        $requestOptions->idempotencyKey(uniqid());
        $this->gateway->updateSubscription($updateRequest, $requestOptions);
        $updateRequest->planId(42);

        // when
        $exception = Assert::catchShift4Exception(function () use ($updateRequest, $requestOptions) {
            $this->gateway->updateSubscription($updateRequest, $requestOptions);
        });

        // then
        Assert::assertSame('Idempotent key used for request with different parameters.', $exception->getMessage());
    }
}
