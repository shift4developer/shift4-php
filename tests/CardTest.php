<?php
require_once 'bootstrap.php';

use Shift4\Request\CardUpdateRequest;
use Shift4\Request\CardListRequest;
use Shift4\Request\CreatedFilter;

class CardTest extends AbstractGatewayTest
{

    public function testCreateCard()
    {
        // given
        $customer = $this->gateway->createCustomer(Data::customerRequest());

        $request = Data::cardRequest()->customerId($customer->getId());
        
        // when
        $card = $this->gateway->createCard($request);
        
        // then
        Assert::assertCard($request, $card);
    }
    
    public function testRetrieveCustomer() {
        // given
        $customer = $this->gateway->createCustomer(Data::customerRequest());
        
        $request = Data::cardRequest()->customerId($customer->getId());
        $card = $this->gateway->createCard($request);
    
        // when
        $card = $this->gateway->retrieveCard($customer->getId(), $card->getId());
    
        // then
        Assert::assertCard($request, $card);
    }
    
    public function testUpdateCard() {
        // given
        $customer = $this->gateway->createCustomer(Data::customerRequest());
        
        $request = Data::cardRequest("4242000000011114")->customerId($customer->getId());
        $card = $this->gateway->createCard($request);
    
        $updateRequest = (new CardUpdateRequest())
            ->customerId($card->getCustomerId())
            ->cardId($card->getId())
            ->expMonth(10)
            ->expYear(date('Y') + 1)
            ->cardholderName('updated-cardholder-name')
            ->addressLine1('updated-address-line-1')
            ->addressLine2('updated-address-line-2')
            ->addressCity('updated-address-city')
            ->addressZip('updated-address-zip')
            ->addressState('updated-address-state')
            ->checkFastCredit(true)
            ->addressCountry('DE');
        
        // when
        $card = $this->gateway->updateCard($updateRequest);
    
        // then
        $request->expMonth($updateRequest->getExpMonth());
        $request->expYear($updateRequest->getExpYear());
        $request->cardholderName($updateRequest->getCardholderName());
        $request->addressLine1($updateRequest->getAddressLine1());
        $request->addressLine2($updateRequest->getAddressLine2());
        $request->addressCity($updateRequest->getAddressCity());
        $request->addressZip($updateRequest->getAddressZip());
        $request->addressState($updateRequest->getAddressState());
        $request->addressCountry($updateRequest->getAddressCountry());
        Assert::assertCard($request, $card);
        Assert::assertTrue($card->getFastCredit()->getSupported());
        Assert::assertNotNull($card->getFastCredit()->getUpdated());
    }
    
    public function testDeleteCard() {
        // given
        $customer = $this->gateway->createCustomer(Data::customerRequest());
        $card = $this->gateway->createCard(Data::cardRequest()->customerId($customer->getId()));
    
        // when
        $this->gateway->deleteCard($card->getCustomerId(), $card->getId());
    
        // then
        $card = $this->gateway->retrieveCard($customer->getId(), $card->getId());
        Assert::assertTrue($card->getDeleted());
    }
    
    public function testListCards() {
        // given
        $customer = $this->gateway->createCustomer(Data::customerRequest());

        $request = Data::cardRequest()->customerId($customer->getId());

        $card = $this->gateway->createCard($request);
        $this->gateway->createCard($request);
        $this->gateway->createCard($request);

        $listRequest = (new CardListRequest())
            ->customerId($customer->getId())
            ->limit(2)
            ->includeTotalCount(true)
            ->created((new CreatedFilter())->gte($card->getCreated()));

        // when
        $list = $this->gateway->listCards($listRequest);

        // then
        self::assertEquals(3, $list->getTotalCount());
        self::assertEquals(2, count($list->getList()));
        foreach ($list->getList() as $card) {
            Assert::assertCard($request, $card);
        }
    }
}
