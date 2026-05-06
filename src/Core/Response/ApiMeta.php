<?php

namespace MasyaSmv\FreedomBrokerApi\Core\Response;

final class ApiMeta
{
    /** @var array<string, array<int, string>> */
    private array $headers;

    /**
     * @param array<string, array<int, string>> $headers
     */
    public function __construct(
        private int $statusCode,
        array $headers = [],
        private int $attempts = 1
    ) {
        $this->headers = $headers;
    }

    public function statusCode(): int
    {
        return $this->statusCode;
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
