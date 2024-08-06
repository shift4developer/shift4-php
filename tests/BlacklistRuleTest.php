<?php
require_once 'bootstrap.php';

use Shift4\Request\BlacklistRuleListRequest;
use Shift4\Request\CreatedFilter;
use Shift4\Util\RequestOptions;

class BlacklistRuleTest extends AbstractGatewayTestBase
{

    function testCreateBlacklistRule()
    {
        // given
        $request = Data::blacklistRuleFingerprintRequest();
        
        // when
        $blacklistRule = $this->gateway->createBlacklistRule($request);
        
        // then
        Assert::assertBlacklistRule($request, $blacklistRule);
    }

    function testRetrieveBlacklistRule() {
        // given
        $request = Data::blacklistRuleFingerprintRequest();
        $blacklistRule = $this->gateway->createBlacklistRule($request);
    
        // when
        $blacklistRule = $this->gateway->retrieveBlacklistRule($blacklistRule->getId());
    
        // then
        Assert::assertBlacklistRule($request, $blacklistRule);
    }
    
    function testDeleteBlacklistRule() {
        // given
        $blacklistRule = $this->gateway->createBlacklistRule(Data::blacklistRuleFingerprintRequest());
    
        // when
        $this->gateway->deleteBlacklistRule($blacklistRule->getId());
    
        // then
        $blacklistRule = $this->gateway->retrieveBlacklistRule($blacklistRule->getId());
        Assert::assertTrue($blacklistRule->getDeleted());
    }
    
    function testListBlacklistRules() {
        // given
        $rule = $this->gateway->createBlacklistRule(Data::blacklistRuleFingerprintRequest());
        $this->gateway->createBlacklistRule(Data::blacklistRuleFingerprintRequest());
        $this->gateway->createBlacklistRule(Data::blacklistRuleFingerprintRequest());
        
        $listRequest = (new BlacklistRuleListRequest())
            ->limit(2)
            ->includeTotalCount(true)
            ->created((new CreatedFilter())->gte($rule->getCreated()));
    
        // when
        $list = $this->gateway->listBlacklistRules($listRequest);
    
        // then
        self::assertTrue($list->getTotalCount() >= 3);
        self::assertEquals(2, count($list->getList()));
        foreach ($list->getList() as $rule) {
            self::assertNotNull($rule->getId());
        }
    }

    function testWillNotCreateDuplicateIfSameIdempotencyKeyIsUsed()
    {
        // given
        $request = Data::blacklistRuleFingerprintRequest();
        $requestOptions = new RequestOptions();
        $requestOptions->idempotencyKey(uniqid());

        // when
        $first_call_response = $this->gateway->createBlacklistRule($request, $requestOptions);
        $second_call_response = $this->gateway->createBlacklistRule($request, $requestOptions);

        // then
        Assert::assertEquals($first_call_response->getId(), $second_call_response->getId());
    }

    function testWillThrowExceptionIfSameIdempotencyKeyIsUsedForTwoDifferentCreateRequests()
    {
        // given
        $request = Data::blacklistRuleFingerprintRequest();
        $requestOptions = new RequestOptions();
        $requestOptions->idempotencyKey(uniqid());

        // when
        $this->gateway->createBlacklistRule($request, $requestOptions);
        $request->email("other@email.com");

        $exception = Assert::catchShift4Exception(function () use ($request, $requestOptions) {
            $this->gateway->createBlacklistRule($request, $requestOptions);
        });

        // then
        Assert::assertSame('Idempotent key used for request with different parameters.', $exception->getMessage());
    }
}
