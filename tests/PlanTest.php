<?php
require_once 'bootstrap.php';

use Shift4\Request\PlanUpdateRequest;
use Shift4\Request\PlanListRequest;
use Shift4\Request\CreatedFilter;

class PlanTest extends AbstractGatewayTestBase
{

    public function testCreatePlan()
    {
        // given
        $otherPlan = $this->gateway->createPlan(Data::planRequest());
        
        $request = Data::planRequest()->recursTo($otherPlan->getId());
        
        // when
        $plan = $this->gateway->createPlan($request);
        
        // then
        Assert::assertPlan($request, $plan);
    }

    public function testRetrievePlan() {
        // given
        $request = Data::planRequest();
        $plan = $this->gateway->createPlan($request);
    
        // when
        $plan = $this->gateway->retrievePlan($plan->getId());
    
        // then
        Assert::assertPlan($request, $plan);
    }
    
    public function testUpdatePlan() {
        // given
        $request = Data::planRequest();
        $plan = $this->gateway->createPlan($request);
        
        $updateRequest = (new PlanUpdateRequest())
            ->planId($plan->getId())
            ->name('updated-name')
            ->statementDescription('updated-statement-description')
            ->metadata(array('updated-key' => 'updated-value'));

        // when
        $plan = $this->gateway->updatePlan($updateRequest);
    
        // then
        $request->name($updateRequest->getName());
        $request->statementDescription($updateRequest->getStatementDescription());
        $request->metadata($updateRequest->getMetadata());
        Assert::assertPlan($request, $plan);
    }
    
    public function testDeletePlan() {
        // given
        $plan = $this->gateway->createPlan(Data::planRequest());
    
        // when
        $this->gateway->deletePlan($plan->getId());
    
        // then
        $plan = $this->gateway->retrievePlan($plan->getId());
        Assert::assertTrue($plan->getDeleted());
    }
    
    public function testListPlans() {
        // given
        $request = Data::planRequest();
        
        $plan = $this->gateway->createPlan($request);
        $this->gateway->createPlan($request);
        $this->gateway->createPlan($request);
        
        $listRequest = (new PlanListRequest())
            ->limit(2)
            ->includeTotalCount(true)
            ->created((new CreatedFilter())->gte($plan->getCreated()));
    
        // when
        $list = $this->gateway->listPlans($listRequest);
    
        // then
        self::assertTrue($list->getTotalCount() >= 3);
        self::assertEquals(2, count($list->getList()));
        foreach ($list->getList() as $plan) {
            Assert::assertPlan($request, $plan);
        }
    }
}
