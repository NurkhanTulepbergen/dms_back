<?php

namespace App\Exceptions;

use Exception;

class BusinessException extends Exception
{
    public int $status_code;

    public function __construct(string $message, int $status_code = 422)
    {
        parent::__construct($message);
        $this->status_code = $status_code;
    }
}
