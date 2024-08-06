<?php
require_once 'bootstrap.php';

use Shift4\Request\CreditUpdateRequest;
use Shift4\Request\CreditListRequest;
use Shift4\Request\CreatedFilter;
use Shift4\Util\RequestOptions;

class CreditTest extends AbstractGatewayTestBase
{

    function testCreateCredit()
    {
        // given
        $request = Data::creditRequest();
        
        // when
        $credit = $this->gateway->createCredit($request);
        
        // then
        Assert::assertCredit($request, $credit);
    }

    function testRetrieveCredit()
    {
        // given
        $request = Data::creditRequest();
        $credit = $this->gateway->createCredit($request);
                
        // when
        $credit = $this->gateway->retrieveCredit($credit->getId());
        
        // then
        Assert::assertCredit($request, $credit);
    }
     
    function testUpdateCredit()
    {
        // given
        $customer = $this->gateway->createCustomer(Data::customerRequest());
        
        $request = Data::creditRequest();
        $credit = $this->gateway->createCredit($request);
        
        $updateRequest = (new CreditUpdateRequest())
            ->creditId($credit->getId())
            ->description('updated-description')
            ->customerId($customer->getId())
            ->metadata(array('updated-key' => 'updated-value'));

        // when
        $credit = $this->gateway->updateCredit($updateRequest);
        
        // then
        $expected = $request;
        $expected->description($updateRequest->getDescription());
        $expected->metadata($updateRequest->getMetadata());
        $expected->customerId($customer->getId());

        Assert::assertCredit($expected, $credit);
    }
    
    function testListCredit()
    {
        // given
        $customer = $this->gateway->createCustomer(Data::customerRequest());

        $creditRequest = Data::creditRequest()->customerId($customer->getId());
        $credit = $this->gateway->createCredit($creditRequest);
        $this->gateway->createCredit($creditRequest);
        $this->gateway->createCredit($creditRequest);
        
        $listRequest = (new CreditListRequest())
            ->limit(2)
            ->includeTotalCount(true)
            ->customerId($customer->getId())
            ->created((new CreatedFilter())->gte($credit->getCreated()));
        
        // when
        $list = $this->gateway->listCredits($listRequest);
        
        // then
        self::assertEquals(3, $list->getTotalCount());
        self::assertEquals(2, count($list->getList()));
        foreach ($list->getList() as $credit) {
            Assert::assertCredit($creditRequest, $credit);
        }
    }
    
    function testCreditRequestViaArray()
    {
        // given
        $request = array(
            'amount' => 499,
            'currency' => 'EUR',
            'card' => array(
                'number' => '4242424242424242',
                'expMonth' => '11',
                'expYear' => strval(date('Y') + 1),
                'cvc' => '123'
            ),
            'metadata' => array()
        );
        
        // when
        $credit = $this->gateway->createCredit($request)->toArray();
    
        // then
        self::assertNotNull($credit['id']);
        self::assertNotNull($credit['created']);
        
        self::assertEquals($request['amount'], $credit['amount']);
        self::assertEquals($request['currency'], $credit['currency']);
        
        self::assertNotNull($credit['card']['id']);
        self::assertNotNull($credit['card']['created']);
        
        self::assertEquals(substr($request['card']['number'], 0, 6), $credit['card']['first6']);
        self::assertEquals(substr($request['card']['number'], -4, 4), $credit['card']['last4']);
        self::assertMatchesRegularExpression('/\w+/', $credit['card']['fingerprint']);
        self::assertMatchesRegularExpression('/\w+/', $credit['card']['type']);
        self::assertMatchesRegularExpression('/\w+/', $credit['card']['brand']);
        self::assertEquals($request['card']['expMonth'], $credit['card']['expMonth']);
        self::assertEquals($request['card']['expYear'], $credit['card']['expYear']);
    }

    function testWillNotCreateDuplicateIfSameIdempotencyKeyIsUsed()
    {
        // given
        $request = Data::creditRequest();
        $requestOptions = new RequestOptions();
        $requestOptions->idempotencyKey(uniqid());

        // when
        $first_call_response = $this->gateway->createCredit($request, $requestOptions);
        $second_call_response = $this->gateway->createCredit($request, $requestOptions);

        // then
        Assert::assertEquals($first_call_response->getId(), $second_call_response->getId());
    }

    function testWillCreateTwoInstancesIfDifferentIdempotencyKeysAreUsed()
    {
        // given
        $request = Data::creditRequest();
        $requestOptions = new RequestOptions();
        $requestOptions->idempotencyKey(uniqid());
        $otherRequestOptions = new RequestOptions();
        $otherRequestOptions->idempotencyKey(uniqid());

        // when
        $first_call_response = $this->gateway->createCredit($request, $requestOptions);
        $second_call_response = $this->gateway->createCredit($request, $otherRequestOptions);

        // then
        Assert::assertNotEquals($first_call_response->getId(), $second_call_response->getId());
    }

    function testWillCreateTwoInstancesIfNoIdempotencyKeysAreUsed()
    {
        // given
        $request = Data::creditRequest();

        // when
        $first_call_response = $this->gateway->createCredit($request);
        $second_call_response = $this->gateway->createCredit($request);

        // then
        Assert::assertNotEquals($first_call_response->getId(), $second_call_response->getId());
    }

    function testWillThrowExceptionIfSameIdempotencyKeyIsUsedForTwoDifferentUpdateRequests() {
        // given
        $request = Data::creditRequest();
        $requestOptions = new RequestOptions();
        $requestOptions->idempotencyKey(uniqid());
        $credit = $this->gateway->createCredit($request);

        $updateRequest = (new CreditUpdateRequest())
            ->creditId($credit->getId())
            ->description('updated-description');

        // when
        $this->gateway->updateCredit($updateRequest, $requestOptions);
        $updateRequest->description('different-description');
        $exception = Assert::catchShift4Exception(function () use ($updateRequest, $requestOptions) {
            $this->gateway->updateCredit($updateRequest, $requestOptions);
        });

        // then
        Assert::assertSame('Idempotent key used for request with different parameters.', $exception->getMessage());
    }
}
