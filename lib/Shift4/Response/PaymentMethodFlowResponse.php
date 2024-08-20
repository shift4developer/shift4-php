<?php

namespace Shift4\Response;

class PaymentMethodFlowResponse extends AbstractResponse
{
    public function getNextAction()
    {
        return $this->get('nextAction');
    }
}