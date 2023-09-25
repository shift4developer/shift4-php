<?php

namespace Shift4\Response;

class PaymentMethodApplePay extends AbstractResponse
{
    public function getCardBrand()
    {
        return $this->get('cardBrand');
    }

    public function getCardType()
    {
        return $this->get('cardType');
    }

    public function getFirst6()
    {
        return $this->get('first6');
    }

    public function getLast4()
    {
        return $this->get('last4');
    }

    public function getAmount()
    {
        return $this->get('amount');
    }

    public function getCurrency()
    {
        return $this->get('currency');
    }
}
