<?php

namespace MasyaSmv\FreedomBrokerApi\Tests\Credentials;

use MasyaSmv\FreedomBrokerApi\Credentials\FreedomCredentials;
use MasyaSmv\FreedomBrokerApi\Credentials\StaticCredentialsProvider;
use PHPUnit\Framework\TestCase;

final class StaticCredentialsProviderTest extends TestCase
{
    public function test_it_returns_runtime_credentials(): void
    {
        $credentials = new FreedomCredentials('public', 'private', 'sid');
        $provider = new StaticCredentialsProvider($credentials);

        $this->assertSame($credentials, $provider->credentials());
        $this->assertSame('public', $provider->credentials()->publicKey());
        $this->assertSame('private', $provider->credentials()->privateKey());
        $this->assertSame('sid', $provider->credentials()->sid());
    }
}
