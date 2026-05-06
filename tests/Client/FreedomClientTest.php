<?php

namespace MasyaSmv\FreedomBrokerApi\Tests\Client;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use MasyaSmv\FreedomBrokerApi\Config\FreedomHttpConfig;
use MasyaSmv\FreedomBrokerApi\Core\Client\FreedomClient;
use MasyaSmv\FreedomBrokerApi\Core\Http\FreedomHttpClient;
use MasyaSmv\FreedomBrokerApi\Core\Http\Signer\V1Signer;
use MasyaSmv\FreedomBrokerApi\Core\Http\Signer\V2Signer;
use MasyaSmv\FreedomBrokerApi\Core\Transport\FreedomTransport;
use PHPUnit\Framework\TestCase;

final class FreedomClientTest extends TestCase
{
    public function test_it_builds_v2_request_and_decodes_response(): void
    {
        $mock = new MockHandler([new Response(200, ['X-Test' => '1'], '{"ok":true}')]);
        $client = new FreedomClient(
            new FreedomTransport(
                new Client(['handler' => HandlerStack::create($mock)]),
                new FreedomHttpConfig(retries: 0, retryDelay: 0),
            ),
            new V2Signer('private-key'),
            'public-key',
            FreedomHttpClient::V2,
            'https://api.test',
        );

        $response = $client->withSid('SID42')->request('getOPQ');

        $request = $mock->getLastRequest();
        parse_str((string)$request->getBody(), $form);

        $this->assertSame('https://api.test/v2/cmd/getOPQ', (string)$request->getUri());
        $this->assertTrue($request->hasHeader('X-NtApi-Sig'));
        $this->assertSame('public-key', $form['apiKey']);
        $this->assertSame('SID42', $form['SID']);
        $this->assertTrue($response->successful());
        $this->assertTrue($response->payloadOrFail()->get('ok'));
    }

    public function test_it_builds_v1_legacy_request_body(): void
    {
        $mock = new MockHandler([new Response(200, [], '{"ok":true}')]);
        $client = new FreedomClient(
            new FreedomTransport(
                new Client(['handler' => HandlerStack::create($mock)]),
                new FreedomHttpConfig(retries: 0, retryDelay: 0),
            ),
            new V1Signer('private-key'),
            '',
            FreedomHttpClient::V1,
            'https://api.test',
        );

        $client->request('ping', ['foo' => 'bar']);

        $request = $mock->getLastRequest();
        parse_str((string)$request->getBody(), $form);
        $payload = json_decode($form['q'], true);

        $this->assertSame('https://api.test', (string)$request->getUri());
        $this->assertSame('ping', $payload['cmd']);
        $this->assertSame(['foo' => 'bar'], $payload['params']);
        $this->assertArrayHasKey('sig', $payload);
    }
}
