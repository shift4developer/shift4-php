<?php

namespace Shift4\Request;

class ChargeUpdateRequest extends AbstractRequest
{

    public function getChargeId()
    {
        return $this->get('chargeId');
    }

    public function chargeId($chargeId)
    {
        return $this->set('chargeId', $chargeId);
    }

    public function getDescription()
    {
        return $this->get('description');
    }

    public function description($description)
    {
        return $this->set('description', $description);
    }

    public function getCustomerId()
    {
        return $this->get('customerId');
    }

    public function customerId($customerId)
    {
        return $this->set('customerId', $customerId);
    }

    /**
     * @return \Shift4\Request\ShippingRequest
     */
    public function getShipping()
    {
        return $this->getObject('shipping', '\Shift4\Request\ShippingRequest');
    }

    public function shipping($shipping)
    {
        return $this->set('shipping', $shipping);
    }

    /**
     * @return \Shift4\Request\FraudDetailsRequest
     */
    public function getFraudDetails()
    {
        return $this->getObject('fraudDetails', '\Shift4\Request\FraudDetailsRequest');
    }

    public function fraudDetails($fraudDetails)
    {
        return $this->set('fraudDetails', $fraudDetails);
    }

    /**
     * @return \Shift4\Request\BillingRequest
     */
    public function getBilling()
    {
        return $this->getObject('billing', '\Shift4\Request\BillingRequest');
    }

    public function billing($billing)
    {
        return $this->set('billing', $billing);
    }

    public function getMetadata()
    {
        return $this->get('metadata');
    }

    public function metadata($metadata)
    {
        return $this->set('metadata', $metadata);
    }

    public function getMerchantAccountId()
    {
        return $this->get('merchantAccountId');
    }

    public function merchantAccountId($merchantAccountId)
    {
        return $this->set('merchantAccountId', $merchantAccountId);
    }
}
