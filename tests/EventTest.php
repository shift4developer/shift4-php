<?php
require_once 'bootstrap.php';

use Shift4\Request\EventListRequest;
use Shift4\Request\CreatedFilter;

class EventTest extends AbstractGatewayTestBase
{

    function testRetrieveEvent() 
    {
        // given
        $chargeRequest = Data::chargeRequest();
        $charge = $this->gateway->createCharge($chargeRequest);
        
        $eventId = $this->gateway->listEvents()->getList()[0]->getId();
        
        // when
        $event = $this->gateway->retrieveEvent($eventId);
        
        // then
        Assert::assertChargeSucceededEvent($chargeRequest, $event);
    }
    
    function testListEvents()
    {
        // given
        $chargeRequest = Data::chargeRequest();
        
        $charge = $this->gateway->createCharge($chargeRequest);
        $this->gateway->createCharge($chargeRequest);
        $this->gateway->createCharge($chargeRequest);
        
        $listRequest = (new EventListRequest())
            ->limit(2)
            ->includeTotalCount(true)
            ->created((new CreatedFilter())->gte($charge->getCreated()));
        
        // when
        $list = $this->gateway->listEvents($listRequest);
    
        // then
        self::assertTrue($list->getTotalCount() >= 3);
        self::assertEquals(2, count($list->getList()));
        foreach ($list->getList() as $event) {
            Assert::assertChargeSucceededEvent($chargeRequest, $event);
        }
    }
}
