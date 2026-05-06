<?php

namespace MasyaSmv\FreedomBrokerApi\Contracts;

use MasyaSmv\FreedomBrokerApi\Core\Http\FreedomHttpClient;
use MasyaSmv\FreedomBrokerApi\Credentials\CredentialsProviderInterface;
use MasyaSmv\FreedomBrokerApi\Credentials\FreedomCredentials;
use MasyaSmv\FreedomBrokerApi\FreedomSession;

interface FreedomManagerInterface
{
    public function connect(string $publicKey, string $privateKey, ?string $sid = null, ?int $version = null): FreedomSession;

    public function withCredentials(
        FreedomCredentials|CredentialsProviderInterface $credentials,
        ?int $version = null
    ): FreedomSession;

    public function client(
        string $publicKey,
        string $privateKey,
        ?string $sid = null,
        ?int $version = null
    ): FreedomHttpClient;

    public function load(
        ?string $publicKey,
        ?string $privateKey,
        ?string $from = null,
        ?string $to = null,
        int $version = FreedomHttpClient::V2
    ): array;
}
