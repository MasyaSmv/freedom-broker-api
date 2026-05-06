<?php

namespace MasyaSmv\FreedomBrokerApi\Credentials;

interface CredentialsProviderInterface
{
    public function credentials(): FreedomCredentials;
}
