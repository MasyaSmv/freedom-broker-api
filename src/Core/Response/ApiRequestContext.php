<?php

namespace MasyaSmv\FreedomBrokerApi\Core\Response;

final class ApiRequestContext
{
    /** @var array<string, mixed> */
    private array $payload;

    /**
     * @param array<string, mixed> $payload
     */
    public function __construct(
        private string $command,
        private string $url,
        private string $method = 'POST',
        private int $version = 2,
        array $payload = []
    ) {
        $this->payload = $payload;
    }

    public function command(): string
    {
        return $this->command;
    }

    public function url(): string
    {
        return $this->url;
    }

    public function method(): string
    {
        return $this->method;
    }

    public function version(): int
    {
        return $this->version;
    }

    /**
     * @return array<string, mixed>
     */
    public function payload(): array
    {
        return $this->payload;
    }
}
