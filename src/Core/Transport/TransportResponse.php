<?php

namespace MasyaSmv\FreedomBrokerApi\Core\Transport;

final class TransportResponse
{
    /** @var array<string, array<int, string>> */
    private array $headers;

    /**
     * @param array<string, array<int, string>> $headers
     */
    public function __construct(
        private int $statusCode,
        private string $body,
        array $headers = [],
        private int $attempts = 1
    ) {
        $this->headers = $headers;
    }

    public function statusCode(): int
    {
        return $this->statusCode;
    }

    public function body(): string
    {
        return $this->body;
    }

    /**
     * @return array<string, array<int, string>>
     */
    public function headers(): array
    {
        return $this->headers;
    }

    public function attempts(): int
    {
        return $this->attempts;
    }
}
