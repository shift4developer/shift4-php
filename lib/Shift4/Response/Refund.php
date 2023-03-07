<?php

namespace Shift4\Response;

class Refund extends AbstractResponse
{

    public function __construct($response)
    {
        parent::__construct($response);
    }

    public function getId()
    {
        return $this->get('id');
    }

    public function getCreated()
    {
        return $this->get('created');
    }

    public function getAmount()
    {
        return $this->get('amount');
    }

    public function getCurrency()
    {
        return $this->get('currency');
    }

    public function getCharge()
    {
        return $this->get('charge');
    }

    public function getReason()
    {
        return $this->get('reason');
    }

    public function getStatus()
    {
        return $this->get('status');
    }
}
