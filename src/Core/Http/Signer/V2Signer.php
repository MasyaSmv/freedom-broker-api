<?php

namespace MasyaSmv\FreedomBrokerApi\Core\Http\Signer;

/**
 * Подпись версии 2: hash_hmac-SHA256 от строки preSign().
 */
final class V2Signer extends AbstractSigner
{
    public function __construct(private string $secret) {}

    public function sign(array $payload): string
    {
        return hash_hmac('sha256', $this->preSign($payload), $this->secret);
    }
}
