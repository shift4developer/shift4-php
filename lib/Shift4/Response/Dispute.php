<?php

namespace Shift4\Response;

class Dispute extends AbstractResponse
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

    public function getUpdated()
    {
        return $this->get('updated');
    }

    public function getAmount()
    {
        return $this->get('amount');
    }

    public function getCurrency()
    {
        return $this->get('currency');
    }

    public function getStatus()
    {
        return $this->get('status');
    }

    public function getReason()
    {
        return $this->get('reason');
    }

    public function getAcceptedAsLost()
    {
        return $this->get('acceptedAsLost');
    }

    /**
     * @return \Shift4\Response\DisputeEvidence
     */
    public function getEvidence()
    {
        return $this->getObject('evidence', '\Shift4\Response\DisputeEvidence');
    }

    /**
     * @return \Shift4\Response\DisputeEvidenceDetails
     */
    public function getEvidenceDetails()
    {
        return $this->getObject('evidenceDetails', '\Shift4\Response\DisputeEvidenceDetails');
    }

    /**
     * @return \Shift4\Response\Charge
     */
    public function getCharge()
    {
        return $this->getObject('charge', '\Shift4\Response\Charge');
    }
}
