<?php

namespace MasyaSmv\FreedomBrokerApi\Tests\Transport;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use MasyaSmv\FreedomBrokerApi\Config\FreedomHttpConfig;
use MasyaSmv\FreedomBrokerApi\Core\Transport\FreedomTransport;
use PHPUnit\Framework\TestCase;

final class FreedomTransportTest extends TestCase
{
    public function test_it_retries_network_exception_until_success(): void
    {
        $mock = new MockHandler([
            new ConnectException('offline', new Request('POST', 'https://api.test')),
            new Response(200, [], '{"ok":true}'),
        ]);

        $transport = new FreedomTransport(
            new Client(['handler' => HandlerStack::create($mock)]),
            new FreedomHttpConfig(retries: 1, retryDelay: 0),
        );

        $response = $transport->postForm('https://api.test/v2/cmd/ping', ['cmd' => 'ping']);

        $this->assertSame(200, $response->statusCode());
        $this->assertSame(2, $response->attempts());
    }

    public function test_it_retries_server_errors_until_success(): void
    {
        $mock = new MockHandler([
            new Response(503, [], '{"message":"retry"}'),
            new Response(200, [], '{"ok":true}'),
        ]);

        $transport = new FreedomTransport(
            new Client(['handler' => HandlerStack::create($mock)]),
            new FreedomHttpConfig(retries: 1, retryDelay: 0),
        );

        $response = $transport->postForm('https://api.test/v2/cmd/ping', ['cmd' => 'ping']);

        $this->assertSame(200, $response->statusCode());
        $this->assertSame(2, $response->attempts());
    }
}
