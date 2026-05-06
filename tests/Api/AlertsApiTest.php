<?php

namespace MasyaSmv\FreedomBrokerApi\Tests\Api;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use MasyaSmv\FreedomBrokerApi\Config\FreedomHttpConfig;
use MasyaSmv\FreedomBrokerApi\Core\Api\AlertsApi;
use MasyaSmv\FreedomBrokerApi\Core\Client\FreedomClient;
use MasyaSmv\FreedomBrokerApi\Core\Http\FreedomHttpClient;
use MasyaSmv\FreedomBrokerApi\Core\Http\Signer\V2Signer;
use MasyaSmv\FreedomBrokerApi\Core\Request\AddPriceAlertRequest;
use MasyaSmv\FreedomBrokerApi\Core\Request\DeletePriceAlertRequest;
use MasyaSmv\FreedomBrokerApi\Core\Request\PriceAlertsRequest;
use MasyaSmv\FreedomBrokerApi\Core\Transport\FreedomTransport;
use PHPUnit\Framework\TestCase;

final class AlertsApiTest extends TestCase
{
    public function test_it_sends_get_alerts_list_command(): void
    {
        $mock = new MockHandler([new Response(200, [], '{"alerts":[]}')]);
        (new AlertsApi($this->client($mock)))->list(new PriceAlertsRequest());

        parse_str((string)$mock->getLastRequest()->getBody(), $form);

        $this->assertSame('getAlertsList', $form['cmd']);
        $this->assertArrayNotHasKey('params', $form);
    }

    public function test_it_sends_get_alerts_list_with_ticker(): void
    {
        $form = $this->sentForm(fn (AlertsApi $api) => $api->list(new PriceAlertsRequest('AAPL.US')));

        $this->assertSame('getAlertsList', $form['cmd']);
        $this->assertSame('AAPL.US', $form['params']['ticker']);
    }

    public function test_it_sends_toggle_price_alert_for_add(): void
    {
        $form = $this->sentForm(fn (AlertsApi $api) => $api->add(
            new AddPriceAlertRequest('FCX.US', '105', 'crossing', 'ltp', 'email', 0, 0),
        ));

        $this->assertSame('togglePriceAlert', $form['cmd']);
        $this->assertSame('FCX.US', $form['params']['ticker']);
        $this->assertSame('105', $form['params']['price']['price']);
        $this->assertSame('crossing', $form['params']['trigger_type']);
    }

    public function test_it_sends_toggle_price_alert_for_delete(): void
    {
        $form = $this->sentForm(fn (AlertsApi $api) => $api->delete(
            new DeletePriceAlertRequest(76, 'ltp', 'email'),
        ));

        $this->assertSame('togglePriceAlert', $form['cmd']);
        $this->assertSame('76', $form['params']['id']);
        $this->assertSame('1', $form['params']['del']);
        $this->assertSame('ltp', $form['params']['quote_type']);
    }

    /**
     * @param callable(AlertsApi): mixed $callback
     * @return array<string, mixed>
     */
    private function sentForm(callable $callback): array
    {
        $mock = new MockHandler([new Response(200, [], '{"alerts":[]}')]);
        $callback(new AlertsApi($this->client($mock)));

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
