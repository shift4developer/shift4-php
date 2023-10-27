<?php
require_once 'bootstrap.php';

use Shift4\Request\BlacklistRuleRequest;
use Shift4\Request\BlacklistRuleListRequest;
use Shift4\Request\CreatedFilter;

class BlacklistRuleTest extends AbstractGatewayTestBase
{

    public function testCreateBlacklistRule()
    {
        // given
        $request = Data::blacklistRuleFingerprintRequest();
        
        // when
        $blacklistRule = $this->gateway->createBlacklistRule($request);
        
        // then
        Assert::assertBlacklistRule($request, $blacklistRule);
    }

    public function testRetrieveBlacklistRule() {
        // given
        $request = Data::blacklistRuleFingerprintRequest();
        $blacklistRule = $this->gateway->createBlacklistRule($request);
    
        // when
        $blacklistRule = $this->gateway->retrieveBlacklistRule($blacklistRule->getId());
    
        // then
        Assert::assertBlacklistRule($request, $blacklistRule);
    }
    
    public function testDeleteBlacklistRule() {
        // given
        $blacklistRule = $this->gateway->createBlacklistRule(Data::blacklistRuleFingerprintRequest());
    
        // when
        $this->gateway->deleteBlacklistRule($blacklistRule->getId());
    
        // then
        $blacklistRule = $this->gateway->retrieveBlacklistRule($blacklistRule->getId());
        Assert::assertTrue($blacklistRule->getDeleted());
    }
    
    public function testListBlacklistRules() {
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
}
