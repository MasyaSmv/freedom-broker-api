<?php

namespace MasyaSmv\FreedomBrokerApi\Tests\Api;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use MasyaSmv\FreedomBrokerApi\Config\FreedomHttpConfig;
use MasyaSmv\FreedomBrokerApi\Core\Api\PortfolioApi;
use MasyaSmv\FreedomBrokerApi\Core\Client\FreedomClient;
use MasyaSmv\FreedomBrokerApi\Core\Http\FreedomHttpClient;
use MasyaSmv\FreedomBrokerApi\Core\Http\Signer\V2Signer;
use MasyaSmv\FreedomBrokerApi\Core\Transport\FreedomTransport;
use PHPUnit\Framework\TestCase;

final class PortfolioApiTest extends TestCase
{
    public function test_it_sends_get_position_json_command(): void
    {
        $fixture = json_decode(
            file_get_contents(__DIR__ . '/../fixtures/portfolio_fixture.json'),
            true,
        );

        $mock = new MockHandler([new Response(200, [], json_encode($fixture))]);
        $response = (new PortfolioApi($this->client($mock)))->info();

        parse_str((string)$mock->getLastRequest()->getBody(), $form);

        $this->assertSame('getPositionJson', $form['cmd']);
        $this->assertArrayNotHasKey('params', $form);

        $payload = $response->payloadOrFail()->data();
        $this->assertCount(1, $payload['result']['ps']['pos']);
        $this->assertCount(1, $payload['result']['ps']['acc']);
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
