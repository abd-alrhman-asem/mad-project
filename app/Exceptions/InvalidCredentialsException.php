<?php

namespace App\Exceptions;

use Exception;

class InvalidCredentialsException extends Exception
{
    public function __construct($message = "The provided credentials are incorrect.")
    {
        parent::__construct($message);
    }
}
