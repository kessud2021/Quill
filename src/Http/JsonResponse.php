<?php

namespace Framework\Http;

/**
 * JSON Response object
 */
class JsonResponse extends Response
{
    /**
     * Create a new JSON response
     *
     * @param mixed $data
     * @param int $status
     * @param array $headers
     */
    public function __construct($data = [], int $status = 200, array $headers = [])
    {
        $headers['Content-Type'] = 'application/json';
        parent::__construct(json_encode($data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT), $status, $headers);
    }
}
