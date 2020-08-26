<?php

namespace Bavfalcon9\Sumo\util;

class MapException extends \Exception
{
    public function __construct($message = "")
    {
        parent::__construct($message, 0, null);
    }
}