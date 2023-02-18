<?php

namespace Shift4\Response;

class ChargeFlow extends AbstractResponse
{

    public function __construct($response)
    {
        parent::__construct($response);
    }

    /**
     * @return \Shift4\Response\ChargeFlowReturn
     */
    public function getChargeFlowReturn()
    {
        return $this->getObject('chargeFlowReturn', '\Shift4\Response\ChargeFlowReturn');
    }

    public function getNextAction()
    {
        return $this->get('nextAction');
    }

    public function getReturnUrl()
    {
        return $this->get('returnUrl');
    }
}
