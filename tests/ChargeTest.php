<?php
require_once 'bootstrap.php';

use Shift4\Request\CaptureRequest;
use Shift4\Request\RefundRequest;
use Shift4\Request\ChargeUpdateRequest;
use Shift4\Request\ChargeListRequest;
use Shift4\Request\CreatedFilter;

class ChargeTest extends AbstractGatewayTestBase
{

    public function testCreateCharge()
    {
        // given
        $request = Data::chargeRequest();
        
        // when
        $charge = $this->gateway->createCharge($request);
        
        // then
        Assert::assertCharge($request, $charge);
    }

    public function testCaptureCharge() {
        // given
        $charge = $this->gateway->createCharge(Data::chargeRequest()->captured(false));
        $request = (new CaptureRequest())->chargeId($charge->getId());
    
        // when
        $charge = $this->gateway->captureCharge($request);
    
        // then
        self::assertEquals(true, $charge->getCaptured());
    }

    public function testRetrieveCharge()
    {
        // given
        $request = Data::chargeRequest();
        $charge = $this->gateway->createCharge($request);
                
        // when
        $charge = $this->gateway->retrieveCharge($charge->getId());
        
        // then
        Assert::assertCharge($request, $charge);
    }

    public function testUpdateCharge()
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
    
    public function testListCharges()
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
    
    public function testChargeRequestViaArray()
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
    
    public function testCreateChargeWithAddress()
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
    
    public function testUpdateChargeWithAddress()
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
    
    public function testDispute()
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
}
