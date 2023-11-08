<?php

namespace Shift4\Request;

class CheckoutRequest extends AbstractRequest
{

    /**
     * @return \Shift4\Request\CheckoutRequestCharge
     */
    public function getCharge()
    {
        return $this->getObject('charge', '\Shift4\Request\CheckoutRequestCharge');
    }

    public function charge($charge)
    {
        return $this->set('charge', $charge);
    }

    /**
     * @return \Shift4\Request\CheckoutRequestSubscription
     */
    public function getSubscription()
    {
        return $this->getObject('subscription', '\Shift4\Request\CheckoutRequestSubscription');
    }

    public function subscription($subscription)
    {
        return $this->set('subscription', $subscription);
    }

    /**
     * @return \Shift4\Request\CheckoutRequestCustomCharge
     */
    public function getCustomCharge()
    {
        return $this->getObject('customCharge', '\Shift4\Request\CheckoutRequestCustomCharge');
    }

    public function customCharge($customCharge)
    {
        return $this->set('customCharge', $customCharge);
    }

    public function getCustomerId()
    {
        return $this->get('customerId');
    }

    public function customerId($customerId)
    {
        return $this->set('customerId', $customerId);
    }

    public function getRememberMe()
    {
        return $this->get('rememberMe');
    }

    public function rememberMe($rememberMe)
    {
        return $this->set('rememberMe', $rememberMe);
    }

    /**
     * @return \Shift4\Request\CheckoutRequestThreeDSecure
     */
    public function getThreeDSecure()
    {
        return $this->getObject('threeDSecure', '\Shift4\Request\CheckoutRequestThreeDSecure');
    }

    public function threeDSecure($threeDSecure)
    {
        return $this->set('threeDSecure', $threeDSecure);
    }

    public function getTermsAndConditionsUrl()
    {
        return $this->get('termsAndConditionsUrl');
    }

    public function termsAndConditionsUrl($termsAndConditionsUrl)
    {
        return $this->set('termsAndConditionsUrl', $termsAndConditionsUrl);
    }
}
