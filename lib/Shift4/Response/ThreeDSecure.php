<?php

namespace Shift4\Response;

class ThreeDSecure extends AbstractResponse
{

    public function getCurrency()
    {
        return $this->get('currency');
    }

    public function getAmount()
    {
        return $this->get('amount');
    }
}