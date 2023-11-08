<?php

namespace Shift4\Response;

class Charge extends AbstractResponse
{

    public function __construct($response)
    {
        parent::__construct($response);
    }

    public function getId()
    {
        return $this->get('id');
    }

    public function getClientObjectId()
    {
        return $this->get('clientObjectId');
    }

    public function getCreated()
    {
        return $this->get('created');
    }

    public function getAmount()
    {
        return $this->get('amount');
    }

    public function getAmountRefunded()
    {
        return $this->get('amountRefunded');
    }

    public function getCurrency()
    {
        return $this->get('currency');
    }

    public function getDescription()
    {
        return $this->get('description');
    }

    public function getStatus()
    {
        return $this->get('status');
    }

    /**
     * @return \Shift4\Response\Card
     */
    public function getCard()
    {
        return $this->getObject('card', '\Shift4\Response\Card');
    }

    /**
     * @return \Shift4\Response\PaymentMethod
     */
    public function getPaymentMethod()
    {
        return $this->getObject('paymentMethod', '\Shift4\Response\PaymentMethod');
    }

    /**
     * @return \Shift4\Response\ChargeFlow
     */
    public function getFlow()
    {
        return $this->getObject('flow', '\Shift4\Response\ChargeFlow');
    }

    public function getCustomerId()
    {
        return $this->get('customerId');
    }

    public function getSubscriptionId()
    {
        return $this->get('subscriptionId');
    }

    public function getCaptured()
    {
        return $this->get('captured');
    }

    public function getRefunded()
    {
        return $this->get('refunded');
    }

    /**
     * @return \Shift4\Response\Refund[]
     */
    public function getRefunds()
    {
        return $this->getObjectsList('refunds', '\Shift4\Response\Refund');
    }

    public function getDisputed()
    {
        return $this->get('disputed');
    }

    /**
     * @return \Shift4\Response\FraudDetails
     */
    public function getFraudDetails()
    {
        return $this->getObject('fraudDetails', '\Shift4\Response\FraudDetails');
    }

    /**
     * @return \Shift4\Response\Shipping
     */
    public function getShipping()
    {
        return $this->getObject('shipping', '\Shift4\Response\Shipping');
    }

    /**
     * @return \Shift4\Response\Billing
     */
    public function getBilling()
    {
        return $this->getObject('billing', '\Shift4\Response\Billing');
    }

    /**
     * @return \Shift4\Response\ThreeDSecureInfo
     */
    public function getThreeDSecureInfo()
    {
        return $this->getObject('threeDSecureInfo', '\Shift4\Response\ThreeDSecureInfo');
    }

    /**
     * @return \Shift4\Response\Dispute
     */
    public function getDispute()
    {
        return $this->getObject('dispute', '\Shift4\Response\Dispute');
    }

    public function getMerchantAccountId()
    {
        return $this->get('merchantAccountId');
    }

    public function getMetadata()
    {
        return $this->get('metadata');
    }

    public function getFailureCode()
    {
        return $this->get('failureCode');
    }

    public function getFailureIssuerDeclineCode()
    {
        return $this->get('failureIssuerDeclineCode');
    }

    public function getFailureMessage()
    {
        return $this->get('failureMessage');
    }
}
