<?php

namespace Shift4\Request;

class ThreeDSecureProcessRequest extends AbstractRequest
{
    public function getAmount()
    {
        return $this->get('amount');
    }

    public function amount($amount)
    {
        return $this->set('amount', $amount);
    }

    public function getCurrency()
    {
        return $this->get('currency');
    }

    public function currency($currency)
    {
        return $this->set('currency', $currency);
    }

    public function getReturnUrl()
    {
        return $this->get('returlUrl');
    }

    public function returnUrl($returnUrl)
    {
        return $this->set('returnUrl', $returnUrl);
    }

    /**
     * @return CardRequest
     */
    public function getCard()
    {
        $card = $this->get('card');

        if (is_array($card)) {
            return $this->getObject('card', CardRequest::class);
        } else {
            return $card;
        }
    }

    public function card($card)
    {
        return $this->set('card', $card);
    }
}
