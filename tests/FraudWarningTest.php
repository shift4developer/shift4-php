<?php
require_once 'bootstrap.php';

use Shift4\Request\CreatedFilter;
use Shift4\Request\FraudWarningListRequest;

class FraudWarningTest extends AbstractGatewayTestBase
{

    function testRetrieveFraudWarning()
    {
        // given
        $fraudWarning = $this->createFraudWarning();
                
        // when
        $retrievedFraudWarning = $this->gateway->retrieveFraudWarning($fraudWarning->getId());
        
        // then
        Assert::assertFraudWarning($fraudWarning, $retrievedFraudWarning);
    }

    function testListFraudWarnings()
    {
        // given
        $this->createFraudWarning();
        sleep(1);
        $firstFraudWarning = $this->createFraudWarning();
        sleep(1);
        $secondFraudWarning = $this->createFraudWarning();
        
        $listRequest = (new FraudWarningListRequest())
            ->limit(2)
            ->includeTotalCount(true)
            ->created((new CreatedFilter())->gte($firstFraudWarning->getCreated()));
        
        // when
        $list = $this->gateway->listFraudWarnings($listRequest);
        
        // then
        self::assertEquals(2, count($list->getList()));
        self::assertEquals(2, $list->getTotalCount());
        self::assertFalse($list->getHasMore());
        Assert::assertFraudWarning($secondFraudWarning, $list->getList()[0]);
        Assert::assertFraudWarning($firstFraudWarning, $list->getList()[1]);
    }
    
    /**
     * @return \Shift4\Response\FraudWarning
     */
    private function createFraudWarning()
    {
        $request = Data::chargeRequest();
        $request->getCard()->number('4242000000000208');
        
        $charge = $this->gateway->createCharge($request);
        
        return $this->waitForFraudWarning($charge);
    }
    
    private function waitForFraudWarning($charge)
    {
        $listRequest = (new FraudWarningListRequest())
            ->charge($charge->getId());
        
        for ($timeout = 20; $timeout > 0; $timeout--) {
            $list = $this->gateway->listFraudWarnings($listRequest);
            if (count($list->getList()) > 0) {
                return $list->getList()[0];
            }
            
            sleep(1);
        }
        
        Assert::fail('Timeout waiting for fraud warning on charge ' . $charge->getId());
    }
    
}
