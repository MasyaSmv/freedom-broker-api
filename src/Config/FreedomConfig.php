<?php

namespace MasyaSmv\FreedomBrokerApi\Config;

use MasyaSmv\FreedomBrokerApi\Core\Http\FreedomHttpClient;

final class FreedomConfig
{
    private string $apiUrl;
    private int $version;
    private FreedomHttpConfig $http;

    public function __construct(
        string $apiUrl = 'https://tradernet.ru/api',
        int $version = FreedomHttpClient::V2,
        ?FreedomHttpConfig $http = null
    ) {
        $this->apiUrl = rtrim($apiUrl, '/');
        $this->version = $version;
        $this->http = $http ?? new FreedomHttpConfig();
    }

    /**
     * @param array<string, mixed> $config
     */
    public static function fromArray(array $config): self
    {
        return new self(
            apiUrl: (string)($config['api_url'] ?? 'https://tradernet.ru/api'),
            version: (int)($config['version'] ?? FreedomHttpClient::V2),
            http: FreedomHttpConfig::fromArray($config['http'] ?? $config),
        );
    }

    public function apiUrl(): string
    {
        return $this->apiUrl;
    }

    public function version(): int
    {
        return $this->version;
    }

    public function http(): FreedomHttpConfig
    {
        return $this->http;
    }
}
