<?php

namespace MasyaSmv\FreedomBrokerApi\Core\Factory;

use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use MasyaSmv\FreedomBrokerApi\Config\FreedomConfig;
use MasyaSmv\FreedomBrokerApi\Core\Client\FreedomClient;
use MasyaSmv\FreedomBrokerApi\Core\Http\FreedomHttpClient;
use MasyaSmv\FreedomBrokerApi\Core\Http\Signer\SignerFactory;
use MasyaSmv\FreedomBrokerApi\Core\Response\ApiResponseDecoder;
use MasyaSmv\FreedomBrokerApi\Core\Transport\FreedomTransport;
use MasyaSmv\FreedomBrokerApi\Credentials\CredentialsProviderInterface;
use MasyaSmv\FreedomBrokerApi\Credentials\FreedomCredentials;
use MasyaSmv\FreedomBrokerApi\Credentials\StaticCredentialsProvider;

final class FreedomClientFactory
{
    private FreedomConfig $config;
    private SignerFactory $signerFactory;
    private ?ClientInterface $http;

    public function __construct(
        ?FreedomConfig $config = null,
        ?SignerFactory $signerFactory = null,
        ?ClientInterface $http = null
    ) {
        $this->config = $config ?? new FreedomConfig();
        $this->signerFactory = $signerFactory ?? new SignerFactory();
        $this->http = $http;
    }

    public function make(
        FreedomCredentials|CredentialsProviderInterface $credentials,
        ?int $version = null
    ): FreedomHttpClient {
        $resolved = $this->resolveCredentials($credentials);
        $apiVersion = $version ?? $this->config->version();

        $client = new FreedomHttpClient(
            http: $this->http ?? new Client($this->config->http()->toGuzzleOptions()),
            signer: $this->signerFactory->make($apiVersion, $resolved->privateKey()),
            apiKey: $resolved->publicKey(),
            version: $apiVersion,
            apiUrl: $this->config->apiUrl(),
        );

        if ($resolved->sid() !== null) {
            return $client->withSid($resolved->sid());
        }

        return $client;
    }

    public function makeApiClient(
        FreedomCredentials|CredentialsProviderInterface $credentials,
        ?int $version = null,
        ?ApiResponseDecoder $decoder = null
    ): FreedomClient {
        $resolved = $this->resolveCredentials($credentials);
        $apiVersion = $version ?? $this->config->version();

        $client = new FreedomClient(
            new FreedomTransport(
                $this->http ?? new Client($this->config->http()->toGuzzleOptions()),
                $this->config->http(),
            ),
            $this->signerFactory->make($apiVersion, $resolved->privateKey()),
            $resolved->publicKey(),
            $apiVersion,
            $this->config->apiUrl(),
            $decoder,
        );

        if ($resolved->sid() !== null) {
            return $client->withSid($resolved->sid());
        }

        return $client;
    }

    private function resolveCredentials(
        FreedomCredentials|CredentialsProviderInterface $credentials
    ): FreedomCredentials {
        $provider = $credentials instanceof FreedomCredentials
            ? new StaticCredentialsProvider($credentials)
            : $credentials;

        return $provider->credentials();
    }
}
