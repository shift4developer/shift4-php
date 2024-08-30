<?php
require_once 'bootstrap.php';

use Shift4\Request\BlacklistRuleRequest;
use Shift4\Request\CustomerRequest;

class ExceptionTest extends AbstractGatewayTestBase
{

    function testFailedCharge()
    {
        // given
        $request = Data::chargeRequest();
        $request->getCard()->number('4024007118468684');

        // when
        $e = Assert::catchShift4Exception(function () use ($request) {
            $this->gateway->createCharge($request);
        });

        // then
        self::assertEquals('card_error', $e->getType());
        self::assertEquals('insufficient_funds', $e->getCode());
        self::assertNotNull($e->getChargeId());
        self::assertEquals('51', $e->getIssuerDeclineCode());
    }
    
    function testFailedCredit()
    {
        // given
        $request = Data::creditRequest();
        $request->getCard()->number("4916018475814056");
        
        // when
        $e = Assert::catchShift4Exception(function () use ($request) {
            $this->gateway->createCredit($request);
        });
            
        // then
        self::assertEquals("card_error", $e->getType());
        self::assertEquals("card_declined", $e->getCode());
        self::assertNotNull($e->getCreditId());
    }
}
