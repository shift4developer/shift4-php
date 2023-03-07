<?php

namespace Shift4\Request;

class DisputeUpdateRequest extends AbstractRequest
{

    public function getDisputeId()
    {
        return $this->get('disputeId');
    }

    public function disputeId($disputeId)
    {
        return $this->set('disputeId', $disputeId);
    }

    /**
     * @return \Shift4\Request\DisputeEvidenceRequest
     */
    public function getEvidence()
    {
        return $this->getObject('evidence', '\Shift4\Request\DisputeEvidenceRequest');
    }

    public function evidence($evidence)
    {
        return $this->set('evidence', $evidence);
    }
}
