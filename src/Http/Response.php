<?php

namespace Framework\Http;

/**
 * HTTP Response object
 */
class Response
{
    /**
     * Response content
     *
     * @var string
     */
    protected string $content;

    /**
     * HTTP status code
     *
     * @var int
     */
    protected int $status;

    /**
     * Response headers
     *
     * @var array
     */
    protected array $headers;

    /**
     * Create a new response
     *
     * @param mixed $content
     * @param int $status
     * @param array $headers
     */
    public function __construct($content = '', int $status = 200, array $headers = [])
    {
        $this->content = (string)$content;
        $this->status = $status;
        $this->headers = $headers;
    }

    /**
     * Get the content
     *
     * @return string
     */
    public function getContent(): string
    {
        return $this->content;
    }

    /**
     * Set the content
     *
     * @param string $content
     * @return self
     */
    public function setContent(string $content): self
    {
        $this->content = $content;
        return $this;
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

    /**
     * Set the status code
     *
     * @param int $status
     * @return self
     */
    public function setStatus(int $status): self
    {
        $this->status = $status;
        return $this;
    }

    /**
     * Get a header
     *
     * @param string $name
     * @return string|null
     */
    public function getHeader(string $name): ?string
    {
        return $this->headers[$name] ?? null;
    }

    /**
     * Set a header
     *
     * @param string $name
     * @param string $value
     * @return self
     */
    public function setHeader(string $name, string $value): self
    {
        $this->headers[$name] = $value;
        return $this;
    }

    /**
     * Get all headers
     *
     * @return array
     */
    public function getHeaders(): array
    {
        return $this->headers;
    }

    /**
     * Send the response
     *
     * @return void
     */
    public function send(): void
    {
        // Send status code
        http_response_code($this->status);

        // Send headers
        foreach ($this->headers as $name => $value) {
            header("{$name}: {$value}");
        }

        // Send content
        echo $this->content;
    }

    /**
     * Convert to string
     *
     * @return string
     */
    public function __toString(): string
    {
        return $this->content;
    }
}
