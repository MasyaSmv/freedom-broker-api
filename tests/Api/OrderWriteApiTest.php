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
use MasyaSmv\FreedomBrokerApi\Core\Request\DelTradeOrderRequest;
use MasyaSmv\FreedomBrokerApi\Core\Request\PutStopLossRequest;
use MasyaSmv\FreedomBrokerApi\Core\Request\PutTradeOrderRequest;
use MasyaSmv\FreedomBrokerApi\Core\Transport\FreedomTransport;
use PHPUnit\Framework\TestCase;

final class OrderWriteApiTest extends TestCase
{
    public function test_it_sends_put_trade_order_command(): void
    {
        $form = $this->sentForm(fn (OrdersApi $api) => $api->place(
            new PutTradeOrderRequest('AAPL.US', 1, 2, 100, 1, 150.5),
        ));

        $this->assertSame('putTradeOrder', $form['cmd']);
        $this->assertSame('AAPL.US', $form['params']['instr_name']);
        $this->assertSame('1', $form['params']['action_id']);
        $this->assertSame('2', $form['params']['order_type_id']);
        $this->assertSame('100', $form['params']['qty']);
        $this->assertSame('150.5', $form['params']['limit_price']);
        $this->assertArrayNotHasKey('stop_price', $form['params']);
    }

    public function test_it_sends_del_trade_order_command(): void
    {
        $form = $this->sentForm(fn (OrdersApi $api) => $api->cancel(
            new DelTradeOrderRequest(2929292929),
        ));

        $this->assertSame('delTradeOrder', $form['cmd']);
        $this->assertSame('2929292929', $form['params']['order_id']);
    }

    public function test_it_sends_put_stop_loss_command(): void
    {
        $form = $this->sentForm(fn (OrdersApi $api) => $api->stopLoss(
            new PutStopLossRequest('SIE.EU', 1.5, null, 5.0),
        ));

        $this->assertSame('putStopLoss', $form['cmd']);
        $this->assertSame('SIE.EU', $form['params']['instr_name']);
        $this->assertSame('1.5', $form['params']['take_profit']);
        $this->assertSame('5', $form['params']['stop_loss_percent']);
        $this->assertArrayNotHasKey('stop_loss', $form['params']);
    }

    /**
     * @param callable(OrdersApi): mixed $callback
     * @return array<string, mixed>
     */
    private function sentForm(callable $callback): array
    {
        $mock = new MockHandler([new Response(200, [], '{"order_id":12345}')]);
        $callback(new OrdersApi($this->client($mock)));

        parse_str((string)$mock->getLastRequest()->getBody(), $form);

        return $form;
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
