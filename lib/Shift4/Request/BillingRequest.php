<?php

namespace Shift4\Request;

class BillingRequest extends AbstractRequest
{

    public function getName()
    {
        return $this->get('name');
    }

    public function name($name)
    {
        return $this->set('name', $name);
    }

    /**
     * @return \Shift4\Request\AddressRequest
     */
    public function getAddress()
    {
        return $this->getObject('address', '\Shift4\Request\AddressRequest');
    }

    public function address($address)
    {
        return $this->set('address', $address);
    }

    public function getVat()
    {
        return $this->get('vat');
    }

    public function vat($vat)
    {
        return $this->set('vat', $vat);
    }

    public function getEmail()
    {
        return $this->get('email');
    }

    public function email($email)
    {
        return $this->set('email', $email);
    }
}
