<?php

namespace MasyaSmv\FreedomBrokerApi\Tests\Api;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use MasyaSmv\FreedomBrokerApi\Config\FreedomHttpConfig;
use MasyaSmv\FreedomBrokerApi\Core\Api\SecuritySessionsApi;
use MasyaSmv\FreedomBrokerApi\Core\Client\FreedomClient;
use MasyaSmv\FreedomBrokerApi\Core\Http\FreedomHttpClient;
use MasyaSmv\FreedomBrokerApi\Core\Http\Signer\V2Signer;
use MasyaSmv\FreedomBrokerApi\Core\Request\SecuritySessionsRequest;
use MasyaSmv\FreedomBrokerApi\Core\Transport\FreedomTransport;
use PHPUnit\Framework\TestCase;

final class SecuritySessionsApiTest extends TestCase
{
    public function test_it_sends_get_security_sessions_command(): void
    {
        $mock = new MockHandler([new Response(200, [], '[]')]);
        (new SecuritySessionsApi($this->client($mock)))->list(new SecuritySessionsRequest());

        parse_str((string)$mock->getLastRequest()->getBody(), $form);

        $this->assertSame('getSecuritySessions', $form['cmd']);
        $this->assertArrayNotHasKey('params', $form);
    }

    private function client(MockHandler $mock): FreedomClient
    {
        return new FreedomClient(
            new FreedomTransport(
                new Client(['handler' => HandlerStack::create($mock)]),
                new FreedomHttpConfig(retries: 0, retryDelay: 0),
            ),
            new V2Signer('private-key'),
            'public-key',
            FreedomHttpClient::V2,
            'https://api.test',
        );
    }
}
