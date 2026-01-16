<?php

namespace Framework\Exceptions;

class HttpException extends \Exception {
    protected $statusCode;

    public function __construct($statusCode = 500, $message = null) {
        $this->statusCode = $statusCode;

        if ($message === null) {
            $message = $this->getDefaultMessage($statusCode);
        }

        parent::__construct($message, $statusCode);
    }

    public function getStatusCode() {
        return $this->statusCode;
    }

    protected function getDefaultMessage($code) {
        $messages = [
            400 => 'Bad Request',
            401 => 'Unauthorized',
            403 => 'Forbidden',
            404 => 'Not Found',
            405 => 'Method Not Allowed',
            422 => 'Unprocessable Entity',
            500 => 'Internal Server Error',
            503 => 'Service Unavailable',
        ];

        return $messages[$code] ?? 'Error';
    }
}
