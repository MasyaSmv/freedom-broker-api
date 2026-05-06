<?php

namespace MasyaSmv\FreedomBrokerApi\Tests\Config;

use MasyaSmv\FreedomBrokerApi\Config\FreedomConfig;
use MasyaSmv\FreedomBrokerApi\Core\Http\FreedomHttpClient;
use PHPUnit\Framework\TestCase;

final class FreedomConfigTest extends TestCase
{
    public function test_it_builds_infrastructure_config_without_credentials(): void
    {
        $config = FreedomConfig::fromArray([
            'api_url' => 'https://api.test/',
            'version' => FreedomHttpClient::V1,
            'http' => [
                'timeout' => 7,
                'connect_timeout' => 3,
                'verify_ssl' => false,
                'user_agent' => 'test-agent',
            ],
            'public_key' => 'must-not-be-used',
            'private_key' => 'must-not-be-used',
        ]);

        $this->assertSame('https://api.test', $config->apiUrl());
        $this->assertSame(FreedomHttpClient::V1, $config->version());
        $this->assertSame(7.0, $config->http()->timeout());
        $this->assertSame(3.0, $config->http()->connectTimeout());
        $this->assertFalse($config->http()->verifySsl());
        $this->assertSame('test-agent', $config->http()->userAgent());
    }
}
