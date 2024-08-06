<?php
require_once 'bootstrap.php';

use Shift4\Request\DisputeListRequest;
use Shift4\Request\CreatedFilter;
use Shift4\Request\DisputeUpdateRequest;
use Shift4\Util\RequestOptions;

class DisputeTest extends AbstractGatewayTestBase
{

    function testRetrieveDispute()
    {
        // given
        $dispute = $this->createDispute();
                
        // when
        $retrievedDispute = $this->gateway->retrieveDispute($dispute->getId());
        
        // then
        Assert::assertDispute($dispute, $retrievedDispute);
    }

    function testUpdateDispute()
    {
        // given
        $dispute = $this->createDispute();
        self::assertFalse($dispute->getEvidenceDetails()->getHasEvidence());
        self::assertEquals(0, $dispute->getEvidenceDetails()->getSubmissionCount());
        
        $fileUpload = $this->gateway->createFileUpload(Data::imageFile(), "dispute_evidence");
        $charge = $this->gateway->createCharge(Data::chargeRequest());
        
        $updateRequest = (new DisputeUpdateRequest())
            ->disputeId($dispute->getId())
            ->evidence(Data::disputeEvidenceRequest($fileUpload, $charge));
        
        // when
        $updatedDispute = $this->gateway->updateDispute($updateRequest);

        // then
        self::assertTrue($updatedDispute->getEvidenceDetails()->getHasEvidence());
        self::assertEquals(1, $updatedDispute->getEvidenceDetails()->getSubmissionCount());
        
        Assert::assertDisputeEvidence($updateRequest->getEvidence(), $updatedDispute->getEvidence());
    }
    
    function testCloseDispute() 
    {
        // given
        $dispute = $this->createDispute();
        
        // when
        $retrieveDispute = $this->gateway->closeDispute($dispute->getId());
        
        // then
        self::assertTrue($retrieveDispute->getAcceptedAsLost());
    }

    function testListDisputes()
    {
        // given
        $this->createDispute();
        sleep(1);
        $firstDispute = $this->createDispute();
        sleep(1);
        $secondDispute = $this->createDispute();
        
        $listRequest = (new DisputeListRequest())
            ->limit(2)
            ->includeTotalCount(true)
            ->created((new CreatedFilter())->gte($firstDispute->getCreated()));
        
        // when
        $list = $this->gateway->listDisputes($listRequest);
        
        // then
        self::assertEquals(2, count($list->getList()));
        self::assertEquals(2, $list->getTotalCount());
        self::assertFalse($list->getHasMore());
        Assert::assertDispute($secondDispute, $list->getList()[0]);
        Assert::assertDispute($firstDispute, $list->getList()[1]);
    }

    private function createDispute()
    {
        $request = Data::chargeRequest();
        $request->getCard()->number('4242000000000018');

        $charge = $this->gateway->createCharge($request);

        $this->waitForChargeback($charge);

        $charge = $this->gateway->retrieveCharge($charge->getId());
        return $charge->getDispute();
    }

    function testWillThrowExceptionIfSameIdempotencyKeyIsUsedForTwoDifferentUpdateRequests() {
        // given
        $dispute = $this->createDispute();
        self::assertFalse($dispute->getEvidenceDetails()->getHasEvidence());
        self::assertEquals(0, $dispute->getEvidenceDetails()->getSubmissionCount());

        $fileUpload = $this->gateway->createFileUpload(Data::imageFile(), "dispute_evidence");
        $charge = $this->gateway->createCharge(Data::chargeRequest());
        $otherCharge = $this->gateway->createCharge(Data::chargeRequest());

        $updateRequest = (new DisputeUpdateRequest())
            ->disputeId($dispute->getId())
            ->evidence(Data::disputeEvidenceRequest($fileUpload, $charge));

        $requestOptions = new RequestOptions();
        $requestOptions->idempotencyKey(uniqid());
        $this->gateway->updateDispute($updateRequest, $requestOptions);
        $updateRequest->evidence(Data::disputeEvidenceRequest($fileUpload, $otherCharge));

        // when
        $exception = Assert::catchShift4Exception(function () use ($updateRequest, $requestOptions) {
            $this->gateway->updateDispute($updateRequest, $requestOptions);
        });

        // then
        Assert::assertSame('Idempotent key used for request with different parameters.', $exception->getMessage());
    }
}
