<?php

namespace Framework\Exception;

/**
 * HTTP exception for abort() calls
 */
class HttpException extends \Exception
{
    /**
     * HTTP status code
     *
     * @var int
     */
    protected int $status;

    /**
     * Create a new HTTP exception
     *
     * @param int $status
     * @param string $message
     */
    public function __construct(int $status = 500, string $message = '')
    {
        $this->status = $status;
        parent::__construct($message);
    }

    /**
     * Get the status code
     *
     * @return int
     */
    public function getStatus(): int
    {
        return $this->status;
    }
}
