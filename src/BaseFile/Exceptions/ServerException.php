<?php
/**
 * Created by PhpStorm.
 * User: LINH
 * Date: 2/6/2020
 * Time: 5:15 PM
 */

namespace App\Exceptions;


class ServerException extends AException
{
    public function __construct($message, $errors)
    {
        $msg =  $message?: "Có lỗi sảy ra";
        parent::__construct($msg, 500, $errors);
    }
}
