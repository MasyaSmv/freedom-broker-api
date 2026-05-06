<?php

namespace MasyaSmv\FreedomBrokerApi\Credentials;

final class FreedomCredentials
{
    private string $publicKey;
    private string $privateKey;
    private ?string $sid;

    public function __construct(string $publicKey, string $privateKey, ?string $sid = null)
    {
        $this->publicKey = $publicKey;
        $this->privateKey = $privateKey;
        $this->sid = $sid;
    }

    public function publicKey(): string
    {
        return $this->publicKey;
    }

    public function privateKey(): string
    {
        return $this->privateKey;
    }

    public function sid(): ?string
    {
        return $this->sid;
    }
}
