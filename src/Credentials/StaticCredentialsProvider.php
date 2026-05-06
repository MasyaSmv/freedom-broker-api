<?php

namespace MasyaSmv\FreedomBrokerApi\Credentials;

final class StaticCredentialsProvider implements CredentialsProviderInterface
{
    private FreedomCredentials $credentials;

    public function __construct(FreedomCredentials $credentials)
    {
        $this->credentials = $credentials;
    }

    public function credentials(): FreedomCredentials
    {
        return $this->credentials;
    }
}
