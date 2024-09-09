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

        $charge1 = $this->gateway->createCharge($chargeRequest);
        $charge2 = $this->gateway->createCharge($chargeRequest);
        $charge3 = $this->gateway->createCharge($chargeRequest);
        $expectedChargeIds = [$charge1->getId(), $charge2->getId(), $charge3->getId()];

        $listRequest = (new EventListRequest())
            ->includeTotalCount(true)
            ->created((new CreatedFilter())->gte($charge1->getCreated()));

        // when
        $list = $this->gateway->listEvents($listRequest);

        // then
        self::assertTrue($list->getTotalCount() >= 3);
        $eventsForCreatedCharges = array_filter($list->getList(), function($charge) use ($expectedChargeIds)
        {
            return in_array($charge->getData()->getId(), $expectedChargeIds);
        });
        self::assertTrue(sizeOf($eventsForCreatedCharges) == 3);
        foreach ($eventsForCreatedCharges as $event) {
            Assert::assertEquals('CHARGE_SUCCEEDED', $event->getType());
        }
    }
}
