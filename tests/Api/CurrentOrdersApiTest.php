<?php

namespace MasyaSmv\FreedomBrokerApi\Tests\Api;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use MasyaSmv\FreedomBrokerApi\Config\FreedomHttpConfig;
use MasyaSmv\FreedomBrokerApi\Core\Api\OrdersApi;
use MasyaSmv\FreedomBrokerApi\Core\Client\FreedomClient;
use MasyaSmv\FreedomBrokerApi\Core\Http\FreedomHttpClient;
use MasyaSmv\FreedomBrokerApi\Core\Http\Signer\V2Signer;
use MasyaSmv\FreedomBrokerApi\Core\Request\CurrentOrdersRequest;
use MasyaSmv\FreedomBrokerApi\Core\Transport\FreedomTransport;
use PHPUnit\Framework\TestCase;

final class CurrentOrdersApiTest extends TestCase
{
    public function test_it_sends_get_notify_order_json_command(): void
    {
        $fixture = json_decode(
            file_get_contents(__DIR__ . '/../fixtures/current_orders_fixture.json'),
            true,
        );

        $mock = new MockHandler([new Response(200, [], json_encode($fixture))]);
        $response = (new OrdersApi($this->client($mock)))->currentOrders(
            new CurrentOrdersRequest(activeOnly: 1),
        );

        parse_str((string)$mock->getLastRequest()->getBody(), $form);

        $this->assertSame('getNotifyOrderJson', $form['cmd']);
        $this->assertSame('1', $form['params']['active_only']);
    }

    public function test_it_sends_empty_params_when_no_filter(): void
    {
        $mock = new MockHandler([new Response(200, [], '[]')]);
        (new OrdersApi($this->client($mock)))->currentOrders(new CurrentOrdersRequest());

        parse_str((string)$mock->getLastRequest()->getBody(), $form);

        $this->assertSame('getNotifyOrderJson', $form['cmd']);
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
