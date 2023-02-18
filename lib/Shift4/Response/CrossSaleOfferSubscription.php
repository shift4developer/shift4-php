<?php

namespace Shift4\Response;

class CrossSaleOfferSubscription extends AbstractResponse
{

    public function __construct($response)
    {
        parent::__construct($response);
    }

    public function getPlanId()
    {
        return $this->get('planId');
    }

    public function getCaptureCharges()
    {
        return $this->get('captureCharges');
    }
}
