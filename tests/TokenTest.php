<?php
require_once 'bootstrap.php';

use Shift4\Request\EventListRequest;
use Shift4\Request\CreatedFilter;

class TokenTest extends AbstractGatewayTestBase
{

    function testCreateToken()
    {
        // given
        $request = Data::tokenRequest();

        // when
        $token = $this->gateway->createToken($request);
    
        // then
        Assert::assertToken($request, $token);
    }
    
    function testRetrieveToken() 
    {
        // given
        $request = Data::tokenRequest();
        $token = $this->gateway->createToken($request);
        
        $this->gateway->createCharge(Data::chargeRequest()->card($token->getId()));
        
        // when
        $token = $this->gateway->retrieveToken($token->getId());
        
        // then
        Assert::assertToken($request, $token);
    }
}
