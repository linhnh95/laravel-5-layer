<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

abstract class AException extends Exception
{
    protected $errors;
    protected $code;
    protected $message = '';

    public function __construct($message = null, $code = Response::HTTP_INTERNAL_SERVER_ERROR, $errors = null)
    {
        $this->code    = $code;
        $this->errors  = $errors;
        $this->message = $message ?: 'Server Exception';

        parent::__construct($message, $code);
    }

    /**
     * Report the exception.
     *
     * @return void
     */
    public function report()
    {
        Log::info($this->message);
    }

    /**
     * Render the exception as an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function render(Request $request)
    {
        $data = [
            'message'     => $this->message,
            'status_code' => $this->code,
        ];
        if ($this->errors) {
            $data['errors'] = $this->errors;
        }
        return response()->json($data, $this->code);
    }
}
