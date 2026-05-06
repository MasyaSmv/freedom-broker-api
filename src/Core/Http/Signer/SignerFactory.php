<?php

namespace MasyaSmv\FreedomBrokerApi\Core\Http\Signer;

use InvalidArgumentException;
use MasyaSmv\FreedomBrokerApi\Core\Http\FreedomHttpClient;

final class SignerFactory
{
    public function make(int $version, string $privateKey): SignerInterface
    {
        if ($version === FreedomHttpClient::V1) {
            return new V1Signer($privateKey);
        }

        if ($version === FreedomHttpClient::V2) {
            return new V2Signer($privateKey);
        }

        throw new InvalidArgumentException("Unsupported Freedom API version [$version].");
    }
}
