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
use MasyaSmv\FreedomBrokerApi\Core\Request\OrderHistoryRequest;
use MasyaSmv\FreedomBrokerApi\Core\Transport\FreedomTransport;
use PHPUnit\Framework\TestCase;

final class OrdersApiTest extends TestCase
{
    public function test_it_sends_order_history_command(): void
    {
        $fixture = json_decode(
            file_get_contents(__DIR__ . '/../fixtures/order_history_fixture.json'),
            true,
        );

        $mock = new MockHandler([new Response(200, [], json_encode($fixture))]);
        $response = (new OrdersApi($this->client($mock)))->history(new OrderHistoryRequest(
            '2020-03-23T00:00:00',
            '2020-04-03T23:59:59',
        ));

        parse_str((string)$mock->getLastRequest()->getBody(), $form);

        $this->assertSame('getOrdersHistory', $form['cmd']);
        $this->assertSame('2020-03-23T00:00:00', $form['params']['from']);
        $this->assertSame('2020-04-03T23:59:59', $form['params']['till']);
        $this->assertArrayNotHasKey('accountId', $form['params']);

        $orders = $response->payloadOrFail()->get('orders');
        $this->assertSame('USER', $orders['key']);
        $this->assertCount(2, $orders['order']);
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
