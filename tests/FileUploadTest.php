<?php
require_once 'bootstrap.php';

use Shift4\Request\FileUploadListRequest;
use Shift4\Request\CreatedFilter;

class FileUploadTest extends AbstractGatewayTestBase
{

    function testCreateFileUpload()
    {
        // given
        $file = Data::imageFile();
        
        // when
        $fileUpload = $this->gateway->createFileUpload($file, 'dispute_evidence');
        
        // then
        Assert::assertFileUpload($file, $fileUpload);
    }

    function testRetrieveFileUpload()
    {
        // given
        $file = Data::imageFile();
        $fileUpload = $this->gateway->createFileUpload($file, 'dispute_evidence');
        
        // when
        $fileUpload = $this->gateway->retrieveFileUpload($fileUpload->getId());
        
        // then
        Assert::assertFileUpload($file, $fileUpload);
    }

    function testListFileUploads()
    {
        // given
        $file = Data::imageFile();
        $this->gateway->createFileUpload($file, 'dispute_evidence');
        sleep(1);
        $firstFileUpload = $this->gateway->createFileUpload($file, 'dispute_evidence');;
        sleep(1);
        $secondFileUpload = $this->gateway->createFileUpload($file, 'dispute_evidence');;
        
        $listRequest = (new FileUploadListRequest())
            ->limit(2)
            ->includeTotalCount(true)
            ->created((new CreatedFilter())->gte($firstFileUpload->getCreated()));
        
        // when
        $list = $this->gateway->listFileUploads($listRequest);
        
        // then
        self::assertEquals(2, count($list->getList()));
        self::assertEquals(2, $list->getTotalCount());
        self::assertFalse($list->getHasMore());
        Assert::assertFileUpload($file, $list->getList()[0]);
        Assert::assertFileUpload($file, $list->getList()[1]);
    }
}
