<?php

namespace Shift4\Request;

class PaymentMethodRequestApplePay extends AbstractRequest
{

    public function getToken()
    {
        return $this->get('token');
    }

    /**
     * @param string $token token received from apple pay
     * @return PaymentMethodRequestApplePay
     */
    public function token($token)
    {
        return $this->set('token', $token);
    }
}
