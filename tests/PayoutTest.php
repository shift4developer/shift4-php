<?php
require_once 'bootstrap.php';

use Shift4\Request\PayoutListRequest;
use Shift4\Request\PayoutTransactionListRequest;
use Shift4\Request\CreatedFilter;
use Shift4\Util\RequestOptions;


class PayoutTest extends AbstractGatewayTestBase
{
    function testCreatePayout() {
        // given
        $this->gateway->createCharge(Data::chargeRequest());

        // when
        $payout = $this->gateway->createPayout();
        
        // then
        Assert::assertPayout($payout);
    }

    function testRetrievePayout() {
        // given
        $this->gateway->createCharge(Data::chargeRequest());
        $payout = $this->gateway->createPayout();
        
        // when
        $payout = $this->gateway->retrievePayout($payout->getId());
        
        // then
        Assert::assertPayout($payout);
    }
    
    function testListPayouts() {
        // given
        $charge = $this->gateway->createCharge(Data::chargeRequest());
        $this->gateway->createPayout();
        
        $this->gateway->createCharge(Data::chargeRequest());
        $this->gateway->createPayout();
        
        $this->gateway->createCharge(Data::chargeRequest());
        $this->gateway->createPayout();
        
        $listRequest = (new PayoutListRequest())
            ->limit(2)
            ->includeTotalCount(true)
            ->created((new CreatedFilter())->gte($charge->getCreated()));
        
        // when
        $list = $this->gateway->listPayouts($listRequest);
        
        // then
        self::assertGreaterThanOrEqual(3, $list->getTotalCount());
        self::assertEquals(2, count($list->getList()));
        foreach ($list->getList() as $payout) {
            Assert::assertPayout($payout);
        }
    }

    function testListPayoutTransactions() {
        // given
        $charge = $this->gateway->createCharge(Data::chargeRequest());
        $this->gateway->createCharge(Data::chargeRequest());
        $this->gateway->createCharge(Data::chargeRequest());
        
        $payout = $this->gateway->createPayout();
        
        $listRequest = (new PayoutTransactionListRequest())
            ->limit(2)
            ->includeTotalCount(true)
            ->created((new CreatedFilter())->gte($charge->getCreated()))
            ->payout($payout->getId());
        
        // when
        $list = $this->gateway->listPayoutTransactions($listRequest);
        
        // then
        self::assertGreaterThanOrEqual(3, $list->getTotalCount());
        self::assertEquals(2, count($list->getList()));
        foreach ($list->getList() as $payoutTransaction) {
            Assert::assertPayoutTransaction($payoutTransaction, $payout);
        }
    }

    function testWillNotCreateDuplicateIfSameIdempotencyKeyIsUsed()
    {
        // given
        $this->gateway->createCharge(Data::chargeRequest());
        $requestOptions = new RequestOptions();
        $requestOptions->idempotencyKey(uniqid());

        // when
        $first_call_response = $this->gateway->createPayout($requestOptions);
        $second_call_response = $this->gateway->createPayout($requestOptions);

        // then
        Assert::assertEquals($first_call_response->getId(), $second_call_response->getId());
    }

    function testWillCreateTwoInstancesIfDifferentIdempotencyKeysAreUsed()
    {
        // given
        $this->gateway->createCharge(Data::chargeRequest());
        $requestOptions = new RequestOptions();
        $requestOptions->idempotencyKey(uniqid());
        $otherRequestOptions = new RequestOptions();
        $otherRequestOptions->idempotencyKey(uniqid());

        // when
        $first_call_response = $this->gateway->createPayout($requestOptions);
        $second_call_response = $this->gateway->createPayout($otherRequestOptions);

        // then
        Assert::assertNotEquals($first_call_response->getId(), $second_call_response->getId());
    }

    function testWillCreateTwoInstancesIfNoIdempotencyKeysAreUsed()
    {
        // given
        $this->gateway->createCharge(Data::chargeRequest());

        // when
        $first_call_response = $this->gateway->createPayout();
        $second_call_response = $this->gateway->createPayout();

        // then
        Assert::assertNotEquals($first_call_response->getId(), $second_call_response->getId());
    }
}
