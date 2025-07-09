<?php

namespace MasyaSmv\FreedomBrokerApi\Core\Http\Signer;

/**
 * Подпись версии 2: сначала SHA‑256 от тела, затем HMAC‑SHA512 от хэша.
 */
final class V2Signer extends AbstractSigner
{
    public function sign(array $payload): string
    {
        $hash = hash('sha256', $this->encode($payload));
        return hash_hmac('sha512', $hash, $this->secret);
    }
}
