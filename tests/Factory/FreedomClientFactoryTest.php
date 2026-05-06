<?php

namespace MasyaSmv\FreedomBrokerApi\Tests\Factory;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use MasyaSmv\FreedomBrokerApi\Config\FreedomConfig;
use MasyaSmv\FreedomBrokerApi\Core\Client\FreedomClient;
use MasyaSmv\FreedomBrokerApi\Core\Factory\FreedomClientFactory;
use MasyaSmv\FreedomBrokerApi\Core\Http\FreedomHttpClient;
use MasyaSmv\FreedomBrokerApi\Credentials\FreedomCredentials;
use PHPUnit\Framework\TestCase;

final class FreedomClientFactoryTest extends TestCase
{
    public function test_it_creates_client_from_runtime_credentials(): void
    {
        $mock = new MockHandler([new Response(200, [], '{"ok":true}')]);
        $factory = new FreedomClientFactory(
            new FreedomConfig('https://api.test', FreedomHttpClient::V2),
            http: new Client(['handler' => HandlerStack::create($mock)]),
        );

        $client = $factory->make(new FreedomCredentials('public-key', 'private-key', 'SID42'));
        $client->request('getOPQ', ['x' => 1], asArray: true);

        $request = $mock->getLastRequest();
        $this->assertSame('https://api.test/v2/cmd/getOPQ', (string)$request->getUri());
        $this->assertTrue($request->hasHeader('X-NtApi-Sig'));

        parse_str((string)$request->getBody(), $form);
        $this->assertSame('public-key', $form['apiKey']);
        $this->assertSame('SID42', $form['SID']);
        $this->assertSame('getOPQ', $form['cmd']);
    }

    public function test_it_creates_normalized_api_client_from_runtime_credentials(): void
    {
        $mock = new MockHandler([new Response(200, [], '{"ok":true}')]);
        $factory = new FreedomClientFactory(
            new FreedomConfig('https://api.test', FreedomHttpClient::V2),
            http: new Client(['handler' => HandlerStack::create($mock)]),
        );

        $client = $factory->makeApiClient(new FreedomCredentials('public-key', 'private-key', 'SID42'));

        $this->assertInstanceOf(FreedomClient::class, $client);

        $response = $client->request('getOPQ');
        $request = $mock->getLastRequest();
        parse_str((string)$request->getBody(), $form);

        $this->assertTrue($response->successful());
        $this->assertSame('public-key', $form['apiKey']);
        $this->assertSame('SID42', $form['SID']);
        $this->assertSame('getOPQ', $form['cmd']);
    }
}
