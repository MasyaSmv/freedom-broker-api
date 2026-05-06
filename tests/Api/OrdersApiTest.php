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
        $mock = new MockHandler([new Response(200, [], '{"orders":[]}')]);
        $response = (new OrdersApi($this->client($mock)))->history(new OrderHistoryRequest(
            'ACC-001',
            '2025-01-01',
            '2025-01-31',
        ));

        parse_str((string)$mock->getLastRequest()->getBody(), $form);

        $this->assertSame('get-orders-history', $form['cmd']);
        $this->assertSame('ACC-001', $form['params']['accountId']);
        $this->assertSame('2025-01-01', $form['params']['from']);
        $this->assertSame('2025-01-31', $form['params']['to']);
        $this->assertSame([], $response->payloadOrFail()->get('orders'));
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
