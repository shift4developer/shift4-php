<?php
require_once 'bootstrap.php';

use Shift4\Request\CheckoutRequest;
use Shift4\Request\CheckoutRequestCharge;
use Shift4\Request\CheckoutRequestCustomCharge;
use Shift4\Request\CheckoutRequestCustomAmount;

class CheckoutTest extends AbstractGatewayTestBase
{

    public function testSignCheckoutRequestWithCharge()
    {
        // given
        $customer = $this->gateway->createCustomer(Data::customerRequest());
        
        $request = Data::checkoutRequestWithCharge($customer);
        
        // when
        $signedRequest = $this->gateway->signCheckoutRequest($request);
        
        // then
        Assert::assertValidCheckoutRequest($signedRequest);
    }
    
    public function testSignCheckoutRequestWithSubscription()
    {
        // given
        $customer = $this->gateway->createCustomer(Data::customerRequest());
        $plan = $this->gateway->createPlan(Data::planRequest());
        
        $request = Data::checkoutRequestWithSubscription($customer, $plan);
        
        // when
        $signedRequest = $this->gateway->signCheckoutRequest($request);
        
        // then
        Assert::assertValidCheckoutRequest($signedRequest);
    }

    public function testSignCheckoutRequestWithCustomCharge()
    {
        // given
        $customer = $this->gateway->createCustomer(Data::customerRequest());
        
        $request = Data::checkoutRequestWithCustomCharge($customer);
        
        // when
        $signedRequest = $this->gateway->signCheckoutRequest($request);

        // then
        Assert::assertValidCheckoutRequest($signedRequest);
    }
}
