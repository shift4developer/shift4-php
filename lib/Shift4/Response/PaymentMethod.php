<?php

namespace Shift4\Response;

class PaymentMethod extends AbstractResponse
{

    public function __construct($response)
    {
        parent::__construct($response);
    }

    public function getId()
    {
        return $this->get('id');
    }

    public function getCustomerId()
    {
        return $this->get('customerId');
    }

    public function getClientObjectId()
    {
        return $this->get('clientObjectId');
    }

    public function getCreated()
    {
        return $this->get('created');
    }

    public function getType()
    {
        return $this->get('type');
    }

    public function getStatus()
    {
        return $this->get('status');
    }

    public function getDeleted()
    {
        return $this->get('deleted', false);
    }

    /**
     * @return \Shift4\Response\Billing
     */
    public function getBilling()
    {
        return $this->getObject('billing', '\Shift4\Response\Billing');
    }

    /**
     * @return \Shift4\Response\FraudCheckData
     */
    public function getFraudCheckData()
    {
        return $this->getObject('fraudCheckData', '\Shift4\Response\FraudCheckData');
    }

    /**
     * @return \Shift4\Response\PaymentMethodApplePay
     */
    public function getApplePay()
    {
        return $this->getObject('applePay', '\Shift4\Response\PaymentMethodApplePay');
    }

    /**
     * @return \Shift4\Response\PaymentMethodGooglePay
     */
    public function getGooglePay()
    {
        return $this->getObject('googlePay', '\Shift4\Response\PaymentMethodGooglePay');
    }

    /**
     * @return \Shift4\Response\ThreeDSecure
     */
    public function getThreeDSecure()
    {
        return $this->getObject('threeDSecure', '\Shift4\Response\ThreeDSecure');
    }

    public function getSource()
    {
        return $this->get('source');
    }

    /**
     * @return \Shift4\Response\PaymentMethodFlowResponse
     */
    public function getFlow()
    {
        return $this->getObject('flow', '\Shift4\Response\PaymentMethodFlowResponse');
    }

}
