<?php

namespace Shift4\Request;

class SubscriptionUpdateRequest extends AbstractRequest
{

    public function getSubscriptionId()
    {
        return $this->get('subscriptionId');
    }

    public function subscriptionId($subscriptionId)
    {
        return $this->set('subscriptionId', $subscriptionId);
    }

    public function getCustomerId()
    {
        return $this->get('customerId');
    }

    public function customerId($customerId)
    {
        return $this->set('customerId', $customerId);
    }

    public function getPlanId()
    {
        return $this->get('planId');
    }

    public function planId($planId)
    {
        return $this->set('planId', $planId);
    }

    /**
     * @return \Shift4\Request\CardRequest
     */
    public function getCard()
    {
        return $this->getObject('card', '\Shift4\Request\CardRequest');
    }

    public function card($card)
    {
        return $this->set('card', $card);
    }

    public function getQuantity()
    {
        return $this->get('quantity');
    }

    public function quantity($quantity)
    {
        return $this->set('quantity', $quantity);
    }

    public function getCaptureCharges()
    {
        return $this->get('captureCharges');
    }

    public function captureCharges($captureCharges)
    {
        return $this->set('captureCharges', $captureCharges);
    }

    public function getCurrentPeriodEnd()
    {
        return $this->get('currentPeriodEnd');
    }

    public function currentPeriodEnd($periodEnd)
    {
        return $this->set('currentPeriodEnd', $periodEnd);
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

    public function getMerchantAccountId()
    {
        return $this->get('merchantAccountId');
    }

    public function merchantAccountId($merchantAccountId)
    {
        return $this->set('merchantAccountId', $merchantAccountId);
    }

    public function getMetadata()
    {
        return $this->get('metadata');
    }

    public function metadata($metadata)
    {
        return $this->set('metadata', $metadata);
    }
}
