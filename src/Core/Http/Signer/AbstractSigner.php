<?php

namespace MasyaSmv\FreedomBrokerApi\Core\Http\Signer;

/**
 * Обеспечивает общую предварительную рутину, описанную в официальной документации API Freedom.
 */
abstract class AbstractSigner implements SignerInterface
{
    /** @var string сервисный ключ API */
    protected string $secret;

    public function __construct(string $secret)
    {
        $this->secret = $secret;
    }

    /**
     * Freedom API требует компактного JSON без лишних слэшей и юникода.
     */
    protected function encode(array $payload): string
    {
        return json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }
}
