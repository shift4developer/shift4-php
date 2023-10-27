<?php
require_once 'bootstrap.php';

use Shift4\Request\RefundRequest;
use Shift4\Request\RefundListRequest;
use Shift4\Request\CreatedFilter;

class RefundTest extends AbstractGatewayTest
{
    
    public function testCreateRefund() {
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

    public function testCreateRefundUsingOldMethod() {
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
    
    public function testRetrieveRefund() {
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
    
    public function testListRefunds() {
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
}
