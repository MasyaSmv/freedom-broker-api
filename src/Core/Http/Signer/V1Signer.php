<?php

namespace MasyaSmv\FreedomBrokerApi\Core\Http\Signer;

/**
 * Подпись версии 1: HMAC‑SHA256 от тела запроса.
 */
final class V1Signer extends AbstractSigner
{
    public function __construct(private string $secret) {}

    public function sign(array $payload): string
    {
        return md5($this->preSign($payload) . $this->secret);
    }
}
