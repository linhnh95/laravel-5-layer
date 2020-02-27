<?php

namespace App\Exceptions;

class ValidationException extends AException
{
    public function __construct($mesage, $errors)
    {
        $message =  $mesage?: "Trường không hợp lệ";
        parent::__construct($message, 422, $errors);
    }
}
