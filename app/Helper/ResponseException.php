<?php

namespace App\Helper;

use Exception;

class ResponseException extends Exception
{
    /**
     * Create a new class instance.
     */
    public function __construct($message = null, $code = 0, Exception $previous = null)
    {
        parent::__construct(json_encode($message), $code, $previous);
    }

    public function getDecodedMessage($assoc = false)
    {
        return json_decode($this->getMessage(), $assoc);
    }
}
