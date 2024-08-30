<?php
require_once 'bootstrap.php';

use Shift4\Request\RefundRequest;
use Shift4\Request\RefundListRequest;
use Shift4\Request\CreatedFilter;
use Shift4\Util\RequestOptions;

class RefundTest extends AbstractGatewayTestBase
{
    
    function testCreateRefund() {
        // given
        $charge = $this->gateway->createCharge(Data::chargeRequest());
        
        $request = (new RefundRequest())
            ->chargeId($charge->getId())
            ->amount(100);

        // when
        $refund = $this->gateway->createRefund($request);
        
        // then
        Assert::assertRefund($request, $charge, $refund);

        $charge = $this->gateway->retrieveCharge($charge->getId());
        
        self::assertEquals(true, $charge->getRefunded());
        self::assertEquals(1, count($charge->getRefunds()));
        Assert::assertRefund($request, $charge->getRefunds()[0], $refund, false);
    }

    function testCreateRefundUsingOldMethod() {
        // given
        $charge = $this->gateway->createCharge(Data::chargeRequest());
        
        $request = (new RefundRequest())
            ->chargeId($charge->getId())
            ->amount(100);
        
        // when
        $refund = $this->gateway->refundCharge($request);
        
        // then
        Assert::assertRefund($request, $charge, $refund);
        
        $charge = $this->gateway->retrieveCharge($charge->getId());
        
        self::assertEquals(true, $charge->getRefunded());
        self::assertEquals(1, count($charge->getRefunds()));
        Assert::assertRefund($request, $charge->getRefunds()[0], $refund, false);
    }
    
    function testRetrieveRefund() {
        // given
        $charge = $this->gateway->createCharge(Data::chargeRequest());
        
        $request = (new RefundRequest())
            ->chargeId($charge->getId())
            ->amount(100);
        $refund = $this->gateway->createRefund($request);
        
        // when
        $refund = $this->gateway->retrieveRefund($refund->getId());
        
        // then
        Assert::assertRefund($request, $charge, $refund);
    }
    
    function testListRefunds() {
        // given
        $charge = $this->gateway->createCharge(Data::chargeRequest());

        $refundRequest = (new RefundRequest())
            ->chargeId($charge->getId())
            ->amount(100);
        
        $refund = $this->gateway->createRefund($refundRequest);
        $this->gateway->createRefund($refundRequest);
        $this->gateway->createRefund($refundRequest);
        
        $listRequest = (new RefundListRequest())
            ->limit(2)
            ->includeTotalCount(true)
            ->created((new CreatedFilter())->gte($charge->getCreated()))
            ->chargeId($charge->getId());
        
        
        // when
        $list = $this->gateway->listRefunds($listRequest);
        
        // then
        self::assertEquals(3, $list->getTotalCount());
        self::assertEquals(2, count($list->getList()));
        foreach ($list->getList() as $refund) {
            Assert::assertRefund($refundRequest, $charge, $refund);
        }
    }

    function testWillNotCreateDuplicateIfSameIdempotencyKeyIsUsed()
    {
        // given
        $charge = $this->gateway->createCharge(Data::chargeRequest());
        $request = (new RefundRequest())
            ->chargeId($charge->getId())
            ->amount(100);
        $requestOptions = new RequestOptions();
        $requestOptions->idempotencyKey(uniqid());

        // when
        $first_call_response = $this->gateway->createRefund($request, $requestOptions);
        $second_call_response = $this->gateway->createRefund($request, $requestOptions);

        // then
        Assert::assertEquals($first_call_response->getId(), $second_call_response->getId());
    }

    function testWillNotCreateDuplicateIfSameIdempotencyKeyIsUsedForOldMethod()
    {
        // given
        $charge = $this->gateway->createCharge(Data::chargeRequest());
        $request = (new RefundRequest())
            ->chargeId($charge->getId())
            ->amount(100);
        $requestOptions = new RequestOptions();
        $requestOptions->idempotencyKey(uniqid());

        // when
        $first_call_response = $this->gateway->refundCharge($request, $requestOptions);
        $second_call_response = $this->gateway->refundCharge($request, $requestOptions);

        // then
        Assert::assertEquals($first_call_response->getId(), $second_call_response->getId());
    }

    function testWillCreateTwoInstancesIfDifferentIdempotencyKeysAreUsed()
    {
        // given
        $charge = $this->gateway->createCharge(Data::chargeRequest());
        $request = (new RefundRequest())
            ->chargeId($charge->getId())
            ->amount(100);
        $requestOptions = new RequestOptions();
        $requestOptions->idempotencyKey(uniqid());
        $otherRequestOptions = new RequestOptions();
        $otherRequestOptions->idempotencyKey(uniqid());

        // when
        $first_call_response = $this->gateway->createRefund($request, $requestOptions);
        $second_call_response = $this->gateway->createRefund($request, $otherRequestOptions);

        // then
        Assert::assertNotEquals($first_call_response->getId(), $second_call_response->getId());
    }

    function testWillCreateTwoInstancesIfNoIdempotencyKeysAreUsed()
    {
        // given
        $charge = $this->gateway->createCharge(Data::chargeRequest());
        $request = (new RefundRequest())
            ->chargeId($charge->getId())
            ->amount(100);

        // when
        $first_call_response = $this->gateway->createRefund($request);
        $second_call_response = $this->gateway->createRefund($request);

        // then
        Assert::assertNotEquals($first_call_response->getId(), $second_call_response->getId());
    }

    function testWillThrowExceptionIfSameIdempotencyKeyIsUsedForTwoDifferentCreateRequests()
    {
        // given
        $charge = $this->gateway->createCharge(Data::chargeRequest());
        $request = (new RefundRequest())
            ->chargeId($charge->getId())
            ->amount(100);
        $requestOptions = new RequestOptions();
        $requestOptions->idempotencyKey(uniqid());

        // when
        $this->gateway->createRefund($request, $requestOptions);
        $request->amount(42);

        $exception = Assert::catchShift4Exception(function () use ($request, $requestOptions) {
            $this->gateway->createRefund($request, $requestOptions);
        });

        // then
        Assert::assertSame('Idempotent key used for request with different parameters.', $exception->getMessage());
    }
}
