<?php

namespace Framework\Http;

class Response {
    protected $body;
    protected $statusCode;
    protected $headers = [];

    public function __construct($body = '', $statusCode = 200, $headers = []) {
        $this->body = $body;
        $this->statusCode = $statusCode;
        $this->headers = array_merge([
            'Content-Type' => 'text/html; charset=UTF-8',
        ], $headers);
    }

    public function getBody() {
        return $this->body;
    }

    public function setBody($body) {
        $this->body = $body;
        return $this;
    }

    public function getStatusCode() {
        return $this->statusCode;
    }

    public function setStatusCode($code) {
        $this->statusCode = $code;
        return $this;
    }

    public function getHeaders() {
        return $this->headers;
    }

    public function setHeader($name, $value) {
        $this->headers[$name] = $value;
        return $this;
    }

    public function json($data, $statusCode = 200) {
        $this->statusCode = $statusCode;
        $this->headers['Content-Type'] = 'application/json';
        $this->body = json_encode($data);
        return $this;
    }

    public function redirect($location, $statusCode = 302) {
        $this->statusCode = $statusCode;
        $this->headers['Location'] = $location;
        return $this;
    }
}
