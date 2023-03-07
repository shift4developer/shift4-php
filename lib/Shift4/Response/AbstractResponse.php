<?php

namespace Shift4\Response;

use Shift4\Exception\MappingException;

class AbstractResponse
{

    private $data;

    public function __construct($dataArray = null)
    {
        if (is_array($dataArray)) {
            $this->data = $dataArray;
        } elseif ($dataArray !== null) {
            throw new MappingException('Constructor parameter must be an array');
        }

    }

    public function get($field, $default = null)
    {
        if (!isset($this->data[$field])) {
            return $default;
        }

        return $this->data[$field];
    }

    public function getObject($field, $className = '\Shift4\Response\AbstractResponse')
    {
        if (!array_key_exists($field, $this->data)) {
            return null;
        }

        return new $className($this->get($field));
    }

    public function getObjectsList($field, $className = '\Shift4\Response\AbstractResponse')
    {
        if (!isset($this->data[$field])) {
            return [];
        }

        $list = [];
        foreach ($this->data[$field] as $value) {
            $list[] = new $className($value);
        }
        return $list;
    }

    public function toArray()
    {
        return $this->data;
    }
}
