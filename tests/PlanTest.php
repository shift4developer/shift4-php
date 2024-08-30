<?php
require_once 'bootstrap.php';

use Shift4\Request\PlanUpdateRequest;
use Shift4\Request\PlanListRequest;
use Shift4\Request\CreatedFilter;
use Shift4\Util\RequestOptions;

class PlanTest extends AbstractGatewayTestBase
{

    function testCreatePlan()
    {
        // given
        $otherPlan = $this->gateway->createPlan(Data::planRequest());
        
        $request = Data::planRequest()->recursTo($otherPlan->getId());
        
        // when
        $plan = $this->gateway->createPlan($request);
        
        // then
        Assert::assertPlan($request, $plan);
    }

    function testRetrievePlan() {
        // given
        $request = Data::planRequest();
        $plan = $this->gateway->createPlan($request);
    
        // when
        $plan = $this->gateway->retrievePlan($plan->getId());
    
        // then
        Assert::assertPlan($request, $plan);
    }
    
    function testUpdatePlan() {
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
    
    function testDeletePlan() {
        // given
        $plan = $this->gateway->createPlan(Data::planRequest());
    
        // when
        $this->gateway->deletePlan($plan->getId());
    
        // then
        $plan = $this->gateway->retrievePlan($plan->getId());
        Assert::assertTrue($plan->getDeleted());
    }
    
    function testListPlans() {
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

    function testWillNotCreateDuplicateIfSameIdempotencyKeyIsUsed()
    {
        // given
        $request = Data::planRequest();
        $requestOptions = new RequestOptions();
        $requestOptions->idempotencyKey(uniqid());

        // when
        $first_call_response = $this->gateway->createPlan($request, $requestOptions);
        $second_call_response = $this->gateway->createPlan($request, $requestOptions);

        // then
        Assert::assertEquals($first_call_response->getId(), $second_call_response->getId());
    }

    function testWillCreateTwoInstancesIfDifferentIdempotencyKeysAreUsed()
    {
        // given
        $request = Data::planRequest();
        $requestOptions = new RequestOptions();
        $requestOptions->idempotencyKey(uniqid());
        $otherRequestOptions = new RequestOptions();
        $otherRequestOptions->idempotencyKey(uniqid());

        // when
        $first_call_response = $this->gateway->createPlan($request, $requestOptions);
        $second_call_response = $this->gateway->createPlan($request, $otherRequestOptions);

        // then
        Assert::assertNotEquals($first_call_response->getId(), $second_call_response->getId());
    }

    function testWillCreateTwoInstancesIfNoIdempotencyKeysAreUsed()
    {
        // given
        $request = Data::planRequest();

        // when
        $first_call_response = $this->gateway->createPlan($request);
        $second_call_response = $this->gateway->createPlan($request);

        // then
        Assert::assertNotEquals($first_call_response->getId(), $second_call_response->getId());
    }

    function testWillThrowExceptionIfSameIdempotencyKeyIsUsedForTwoDifferentCreateRequests()
    {
        // given
        $request = Data::planRequest();
        $requestOptions = new RequestOptions();
        $requestOptions->idempotencyKey(uniqid());

        // when
        $this->gateway->createPlan($request, $requestOptions);
        $request->name("Other name");

        $exception = Assert::catchShift4Exception(function () use ($request, $requestOptions) {
            $this->gateway->createPlan($request, $requestOptions);
        });

        // then
        Assert::assertSame('Idempotent key used for request with different parameters.', $exception->getMessage());
    }

    function testWillThrowExceptionIfSameIdempotencyKeyIsUsedForTwoDifferentUpdateRequests() {
        // given
        $request = Data::planRequest();
        $plan = $this->gateway->createPlan($request);

        $updateRequest = (new PlanUpdateRequest())
            ->planId($plan->getId())
            ->name('updated-name')
            ->statementDescription('updated-statement-description')
            ->metadata(array('updated-key' => 'updated-value'));

        $requestOptions = new RequestOptions();
        $requestOptions->idempotencyKey(uniqid());
        $this->gateway->updatePlan($updateRequest, $requestOptions);
        $updateRequest->name('other-name');

        // when
        $exception = Assert::catchShift4Exception(function () use ($updateRequest, $requestOptions) {
            $this->gateway->updatePlan($updateRequest, $requestOptions);
        });

        // then
        Assert::assertSame('Idempotent key used for request with different parameters.', $exception->getMessage());
    }
}
