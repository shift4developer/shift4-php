<?php

namespace Shift4\Response;

class ThreeDSecureProcess extends AbstractResponse
{
    public function __construct($response)
    {
        parent::__construct($response);
    }

    public function getEnrolled()
    {
        return $this->get('enrolled');
    }

    public function getVersion()
    {
        return $this->get('version');
    }

    public function getRedirectUrl()
    {
        return $this->get('redirectUrl');
    }

    /**
     * @return \Shift4\Response\Token
     */
    public function getToken()
    {
        return $this->getObject('token', '\Shift4\Response\Token');
    }
}
