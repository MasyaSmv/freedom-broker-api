<?php

namespace MasyaSmv\FreedomBrokerApi\Config;

final class FreedomHttpConfig
{
    private float $timeout;
    private float $connectTimeout;
    private int $retries;
    private int $retryDelay;
    private bool $verifySsl;
    private string $userAgent;

    public function __construct(
        float $timeout = 15.0,
        float $connectTimeout = 10.0,
        int $retries = 0,
        int $retryDelay = 100,
        bool $verifySsl = true,
        string $userAgent = 'freedom-broker-api'
    ) {
        $this->timeout = $timeout;
        $this->connectTimeout = $connectTimeout;
        $this->retries = $retries;
        $this->retryDelay = $retryDelay;
        $this->verifySsl = $verifySsl;
        $this->userAgent = $userAgent;
    }

    /**
     * @param array<string, mixed> $config
     */
    public static function fromArray(array $config): self
    {
        return new self(
            timeout: (float)($config['timeout'] ?? 15.0),
            connectTimeout: (float)($config['connect_timeout'] ?? 10.0),
            retries: (int)($config['retries'] ?? 0),
            retryDelay: (int)($config['retry_delay'] ?? 100),
            verifySsl: (bool)($config['verify_ssl'] ?? true),
            userAgent: (string)($config['user_agent'] ?? 'freedom-broker-api'),
        );
    }

    public function timeout(): float
    {
        return $this->timeout;
    }

    public function connectTimeout(): float
    {
        return $this->connectTimeout;
    }

    public function retries(): int
    {
        return $this->retries;
    }

    public function retryDelay(): int
    {
        return $this->retryDelay;
    }

    public function verifySsl(): bool
    {
        return $this->verifySsl;
    }

    public function userAgent(): string
    {
        return $this->userAgent;
    }

    /**
     * @return array<string, mixed>
     */
    public function toGuzzleOptions(): array
    {
        return [
            'timeout' => $this->timeout,
            'connect_timeout' => $this->connectTimeout,
            'verify' => $this->verifySsl,
            'headers' => [
                'User-Agent' => $this->userAgent,
            ],
        ];
    }
}
