<?php

namespace Shift4\Util;


class RequestOptions
{
    private $idempotencyKey = null;

    public function __construct()
    {
    }

    public function idempotencyKey($idempotencyKey) {
        $this->idempotencyKey = $idempotencyKey;
    }

    public function hasIdempotencyKey() {
        return $this->idempotencyKey !== null;
    }

    public function getIdempotencyKey() {
        return $this->idempotencyKey;
    }
}