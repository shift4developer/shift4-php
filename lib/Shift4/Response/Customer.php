<?php

namespace Shift4\Response;

class Customer extends AbstractResponse
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

    public function getDeleted()
    {
        return $this->get('deleted', false);
    }

    public function getEmail()
    {
        return $this->get('email');
    }

    public function getDescription()
    {
        return $this->get('description');
    }

    public function getDefaultCardId()
    {
        return $this->get('defaultCardId');
    }

    /**
     * @return \Shift4\Response\Card[]
     */
    public function getCards()
    {
        return $this->getObjectsList('cards', '\Shift4\Response\Card');
    }

    public function getMetadata()
    {
        return $this->get('metadata');
    }

    /**
     * @return \Shift4\Response\Card
     */
    public function getDefaultCard()
    {
        foreach ($this->getCards() as $card) {
            if ($card->getId() == $this->getDefaultCardId()) {
                return $card;
            }
        }

        return null;
    }
}
