<?php
require_once 'bootstrap.php';

use Shift4\Request\CustomerUpdateRequest;
use Shift4\Request\CustomerListRequest;
use Shift4\Request\CreatedFilter;
use Shift4\Util\RequestOptions;

class CustomerTest extends AbstractGatewayTestBase
{

    function testCreateCustomer()
    {
        // given
        $request = Data::customerRequest()->card(Data::cardRequest());
        
        // when
        $customer = $this->gateway->createCustomer($request);
        
        // then
        Assert::assertCustomer($request, $customer);
    }

    function testRetrieveCustomer() {
        // given
        $request = Data::customerRequest();
        $customer = $this->gateway->createCustomer($request);
    
        // when
        $customer = $this->gateway->retrieveCustomer($customer->getId());
    
        // then
        Assert::assertCustomer($request, $customer);
    }
    
    function testUpdateCustomer() {
        // given
        $request = Data::customerRequest();
        $customer = $this->gateway->createCustomer($request);
    
        $newCard = Data::cardRequest();
        
        $updateRequest = (new CustomerUpdateRequest())
            ->customerId($customer->getId())
            ->email('updated-email@test.com')
            ->description('updated-description')
            ->card($newCard)
            ->metadata(array('updated-key' => 'updated-value'));
    
        // when
        $customer = $this->gateway->updateCustomer($updateRequest);
    
        // then
        $request->email($updateRequest->getEmail());
        $request->description($updateRequest->getDescription());
        $request->metadata($updateRequest->getMetadata());
        $request->card($newCard);
        Assert::assertCustomer($request, $customer);
    }
    
    function testUpdateCustomerUsingArray() {
        // given
        $request = Data::customerRequest();
        $customer = $this->gateway->createCustomer($request);
        
        $newCard = Data::cardRequest();
        
        $updateRequest = array(
            'customerId' => $customer->getId(),
            'email' => 'updated-email@test.com',
            'description' => 'updated-description',
            'metadata' => array('updated-key' => 'updated-value')
        );
        
        // when
        $customer = $this->gateway->updateCustomer($updateRequest);
        
        // then
        $request->email($updateRequest['email']);
        $request->description($updateRequest['description']);
        $request->metadata($updateRequest['metadata']);
        Assert::assertCustomer($request, $customer);
    }
    
    function testUpdateDefaultCard() {
        // given
        $customer = $this->gateway->createCustomer(Data::customerRequest()->card(Data::cardRequest()));

        $newCardRequest = Data::cardRequest()->customerId($customer->getId());
        $newCard = $this->gateway->createCard($newCardRequest);
        
        $updateRequest = (new CustomerUpdateRequest())
            ->customerId($customer->getId())
            ->defaultCardId($newCard->getId());
            
        // when
        $customer = $this->gateway->updateCustomer($updateRequest);
        
        // then
        self::assertEquals($newCard->getId(), $customer->getDefaultCardId());
        self::assertEquals($newCard->getId(), $customer->getDefaultCard()->getId());
        Assert::assertCard($newCardRequest, $customer->getDefaultCard());
    }
    
    function testDeleteCustomer() {
        // given
        $customer = $this->gateway->createCustomer(Data::customerRequest());
    
        // when
        $this->gateway->deleteCustomer($customer->getId());
    
        // then
        $customer = $this->gateway->retrieveCustomer($customer->getId());
        Assert::assertTrue($customer->getDeleted());
    }
    
    function testListCustomers() {
        // given
        $request = Data::customerRequest();
        
        $customer = $this->gateway->createCustomer($request);
        $this->gateway->createCustomer($request);
        $this->gateway->createCustomer($request);
        
        $listRequest = (new CustomerListRequest())
            ->limit(2)
            ->includeTotalCount(true)
            ->created((new CreatedFilter())->gte($customer->getCreated()));
    
        // when
        $list = $this->gateway->listCustomers($listRequest);
    
        // then
        self::assertTrue($list->getTotalCount() >= 3);
        self::assertEquals(2, count($list->getList()));
        foreach ($list->getList() as $customer) {
            Assert::assertCustomer($request, $customer);
        }
    }

    function testWillNotCreateDuplicateIfSameIdempotencyKeyIsUsed()
    {
        // given
        $request = Data::customerRequest();
        $requestOptions = new RequestOptions();
        $requestOptions->idempotencyKey(uniqid());

        // when
        $first_call_response = $this->gateway->createCustomer($request, $requestOptions);
        $second_call_response = $this->gateway->createCustomer($request, $requestOptions);

        // then
        Assert::assertEquals($first_call_response->getId(), $second_call_response->getId());
    }

    function testWillCreateTwoInstancesIfDifferentIdempotencyKeysAreUsed()
    {
        // given
        $request = Data::customerRequest();
        $requestOptions = new RequestOptions();
        $requestOptions->idempotencyKey(uniqid());
        $otherRequestOptions = new RequestOptions();
        $otherRequestOptions->idempotencyKey(uniqid());

        // when
        $first_call_response = $this->gateway->createCustomer($request, $requestOptions);
        $second_call_response = $this->gateway->createCustomer($request, $otherRequestOptions);

        // then
        Assert::assertNotEquals($first_call_response->getId(), $second_call_response->getId());
    }

    function testWillCreateTwoInstancesIfNoIdempotencyKeysAreUsed()
    {
        // given
        $request = Data::customerRequest();

        // when
        $first_call_response = $this->gateway->createCustomer($request);
        $second_call_response = $this->gateway->createCustomer($request);

        // then
        Assert::assertNotEquals($first_call_response->getId(), $second_call_response->getId());
    }

    function testWillThrowExceptionIfSameIdempotencyKeyIsUsedForTwoDifferentUpdateRequests() {
        // given
        $request = Data::customerRequest();
        $customer = $this->gateway->createCustomer($request);

        $requestOptions = new RequestOptions();
        $requestOptions->idempotencyKey(uniqid());

        $updateRequest = (new CustomerUpdateRequest())
            ->customerId($customer->getId())
            ->description('updated-description');

        // when
        $this->gateway->updateCustomer($updateRequest, $requestOptions);
        $updateRequest->description('different-description');
        $exception = Assert::catchShift4Exception(function () use ($updateRequest, $requestOptions) {
            $this->gateway->updateCustomer($updateRequest, $requestOptions);
        });

        // then
        Assert::assertSame('Idempotent key used for request with different parameters.', $exception->getMessage());
    }
}
