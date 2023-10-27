<?php
require_once 'bootstrap.php';

use Shift4\Shift4Gateway;
use Shift4\Connection\Connection;;
use Shift4\Connection\CurlConnection;
use Shift4\Request\ChargeRequest;

abstract class AbstractGatewayTestBase extends \PHPUnit\Framework\TestCase
{
    /**
     * @var Connection
     */
    protected $connection;

    /**
     * @var Shift4Gateway
     */
    protected $gateway;

    protected function setUp(): void
    {
        $this->connection = new CurlConnection(array(CURLOPT_SSL_VERIFYPEER => false));

        $this->gateway = new Shift4Gateway(SECRET_KEY);
        $this->gateway->setEndpoint(GATEWAY_URL);
        $this->gateway->setUploadsEndpoint(UPLOADS_URL);
        $this->gateway->setConnection($this->connection);
    }

    protected function waitForChargeback($charge)
    {
        for ($timeout = 20; $timeout > 0; $timeout--) {
            $response = $this->gateway->retrieveCharge($charge->getId());
            if ($response->getDisputed() == true) {
                return;
            }
            
            sleep(1);
        }
        
        Assert::fail('Timeout waiting for dispute on charge ' . $charge->getId());
    }
}
