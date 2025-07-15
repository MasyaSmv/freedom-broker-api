<?php

namespace MasyaSmv\FreedomBrokerApi\Contracts;

use MasyaSmv\FreedomBrokerApi\Core\Http\FreedomHttpClient;

interface FreedomManagerInterface
{
    public function load(
        string $publicKey,
        string $privateKey,
        ?string $from = null,
        ?string $to = null,
        int $version = FreedomHttpClient::V2
    ): array;
}
