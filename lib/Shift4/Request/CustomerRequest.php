<?php

namespace Shift4\Request;

class CustomerRequest extends AbstractRequest
{

    public function getEmail()
    {
        return $this->get('email');
    }

    public function email($email)
    {
        return $this->set('email', $email);
    }

    public function getDescription()
    {
        return $this->get('description');
    }

    public function description($description)
    {
        return $this->set('description', $description);
    }

    /**
     * @return \Shift4\Request\CardRequest
     */
    public function getCard()
    {
        return $this->getObject('card', '\Shift4\Request\CardRequest');
    }

    /**
     * @return \Shift4\Request\PaymentMethodRequest
     */
    public function getPaymentMethod()
    {
        return $this->getObject('paymentMethod', '\Shift4\Request\PaymentMethodRequest');
    }

    public function card($card)
    {
        return $this->set('card', $card);
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
