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

        $events = $this->gateway->listEvents();
        $expectedEvent = array_filter($events->getList(), function ($event) use ($charge) {
            return $event->getType() === 'CHARGE_SUCCEEDED' && $event->getData()->getId() === $charge->getId();
        })[0];

        // when
        $event = $this->gateway->retrieveEvent($expectedEvent->getId());

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
            ->limit(100);

        // when
        $list = $this->gateway->listEvents($listRequest);

        // then
        self::assertGreaterThanOrEqual(3, $list->getTotalCount());
        $eventsForCreatedCharges = array_filter($list->getList(), function ($event) use ($expectedChargeIds) {
            return $event->getType() === 'CHARGE_SUCCEEDED' && in_array($event->getData()->getId(), $expectedChargeIds);
        });
        self::assertEquals(3, sizeOf($eventsForCreatedCharges));
    }
}
