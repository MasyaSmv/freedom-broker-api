<?php

namespace MasyaSmv\FreedomBrokerApi\Core\Http\Signer;

/**
 * Подпись версии 1: HMAC‑SHA256 от тела запроса.
 */
final class V1Signer extends AbstractSigner
{
    public function sign(array $payload): string
    {
        return hash_hmac('sha256', $this->encode($payload), $this->secret);
    }
}
