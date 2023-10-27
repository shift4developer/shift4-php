<?php
require_once 'bootstrap.php';

use Shift4\Request\PayoutRequest;
use Shift4\Request\PayoutListRequest;
use Shift4\Request\PayoutTransactionListRequest;
use Shift4\Request\CreatedFilter;

class PayoutTest extends AbstractGatewayTest
{
    
    public function testCreatePayout() {
        // given
        $charge = $this->gateway->createCharge(Data::chargeRequest());

        // when
        $payout = $this->gateway->createPayout();
        
        // then
        Assert::assertPayout($payout);
    }

    public function testRetrievePayout() {
        // given
        $charge = $this->gateway->createCharge(Data::chargeRequest());
        $payout = $this->gateway->createPayout();
        
        // when
        $payout = $this->gateway->retrievePayout($payout->getId());
        
        // then
        Assert::assertPayout($payout);
    }
    
    public function testListPayouts() {
        // given
        $charge = $this->gateway->createCharge(Data::chargeRequest());
        $payout = $this->gateway->createPayout();
        
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

    public function testListPayoutTransactions() {
        // given
        $charge = $this->gateway->createCharge(Data::chargeRequest());
        $charge2 = $this->gateway->createCharge(Data::chargeRequest());
        $charge3 = $this->gateway->createCharge(Data::chargeRequest());
        
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
}
