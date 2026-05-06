<?php

namespace MasyaSmv\FreedomBrokerApi\Tests\Laravel;

use DateTimeImmutable;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use MasyaSmv\FreedomBrokerApi\Config\FreedomConfig;
use MasyaSmv\FreedomBrokerApi\Core\Factory\FreedomClientFactory;
use MasyaSmv\FreedomBrokerApi\Core\Http\FreedomHttpClient;
use MasyaSmv\FreedomBrokerApi\FreedomSession;
use MasyaSmv\FreedomBrokerApi\Laravel\FreedomManager;
use PHPUnit\Framework\TestCase;

final class FreedomManagerTest extends TestCase
{
    public function test_connect_returns_session_without_config_credentials(): void
    {
        $mock = new MockHandler([new Response(200, [], '{"ok":true}')]);
        $manager = new FreedomManager(new FreedomClientFactory(
            new FreedomConfig('https://api.test', FreedomHttpClient::V2),
            http: new Client(['handler' => HandlerStack::create($mock)]),
        ));

        $session = $manager->connect('public-key', 'private-key');

        $this->assertInstanceOf(FreedomSession::class, $session);
    }

    public function test_client_uses_runtime_credentials(): void
    {
        $mock = new MockHandler([new Response(200, [], '{"ok":true}')]);
        $manager = new FreedomManager(new FreedomClientFactory(
            new FreedomConfig('https://api.test', FreedomHttpClient::V2),
            http: new Client(['handler' => HandlerStack::create($mock)]),
        ));

        $client = $manager->client('public-key', 'private-key');
        $client->request('getOPQ', asArray: true);

        parse_str((string)$mock->getLastRequest()->getBody(), $form);
        $this->assertSame('public-key', $form['apiKey']);
    }

    public function test_connect_exposes_normalized_validate_credentials_flow(): void
    {
        $mock = new MockHandler([new Response(200, [], '{"ok":true,"account":"A1"}')]);
        $manager = new FreedomManager(new FreedomClientFactory(
            new FreedomConfig('https://api.test', FreedomHttpClient::V2),
            http: new Client(['handler' => HandlerStack::create($mock)]),
        ));

        $response = $manager->connect('public-key', 'private-key')->validateCredentials();

        $this->assertTrue($response->successful());
        $this->assertSame('A1', $response->payloadOrFail()->get('account'));
    }

    public function test_connect_exposes_typed_account_profile(): void
    {
        $mock = new MockHandler([new Response(200, [], json_encode([
            'plainAccountInfoData' => [
                'client_code' => 'TEST1234',
                'client_name' => 'John Doe',
                'base_currency' => 'USD',
            ],
        ]))]);
        $manager = new FreedomManager(new FreedomClientFactory(
            new FreedomConfig('https://api.test', FreedomHttpClient::V2),
            http: new Client(['handler' => HandlerStack::create($mock)]),
        ));

        $profile = $manager->connect('public-key', 'private-key')->accountProfile();

        $this->assertSame('TEST1234', $profile->clientCode);
        $this->assertSame('John Doe', $profile->clientName);
        $this->assertSame('USD', $profile->baseCurrency);
    }

    public function test_legacy_load_uses_normalized_report_flow_and_preserves_result_shape(): void
    {
        $fixture = file_get_contents(__DIR__.'/../fixtures/report_fixture.json');
        $mock = new MockHandler([new Response(200, [], $fixture)]);
        $manager = new FreedomManager(new FreedomClientFactory(
            new FreedomConfig('https://api.test', FreedomHttpClient::V2),
            http: new Client(['handler' => HandlerStack::create($mock)]),
        ));

        $result = $manager->load('public-key', 'private-key', '2024-01-01', '2024-01-31');

        $request = $mock->getLastRequest();
        parse_str((string)$request->getBody(), $form);

        $this->assertSame('getBrokerReport', $form['cmd']);
        $this->assertSame('2024-01-01', $form['params']['date_start']);
        $this->assertSame('2024-01-31', $form['params']['date_end']);
        $this->assertSame(
            ['plain', 'operations', 'commissions', 'payments', 'positions', 'balances', 'summary', 'period'],
            array_keys($result),
        );
        $this->assertSame('TEST1234', $result['plain']->clientCode);
    }

    public function test_connect_exposes_typed_securities_and_market_methods(): void
    {
        $mock = new MockHandler([
            new Response(200, [], '{"securities":[{"ticker":"ABC.US","n":123,"short_name":"ABC","instr_type_c":1,"curr":"USD","l":10.5}]}'),
            new Response(200, [], '{"c":"ABC.US","n":123,"short_name":"ABC","instr_type_c":1,"curr":"USD","l":10.5}'),
            new Response(200, [], '{"hloc":{"ABC.US":[[12,9,10,11]]},"xSeries":{"ABC.US":["2024-01-02"]}}'),
        ]);
        $manager = new FreedomManager(new FreedomClientFactory(
            new FreedomConfig('https://api.test', FreedomHttpClient::V2),
            http: new Client(['handler' => HandlerStack::create($mock)]),
        ));

        $session = $manager->connect('public-key', 'private-key');
        $stocks = $session->securitiesList();
        $stock = $session->quoteInfo('ABC.US');
        $history = $session->quoteHistory(
            'ABC.US',
            new DateTimeImmutable('2024-01-01'),
            new DateTimeImmutable('2024-01-31'),
        );

        $this->assertSame('ABC.US', $stocks[0]->ticker);
        $this->assertSame('ABC.US', $stock?->ticker);
        $this->assertSame(10.0, $history[0]->open);
        $this->assertSame(12.0, $history[0]->high);
    }

    public function test_connect_exposes_typed_order_history(): void
    {
        $mock = new MockHandler([
            new Response(200, [], '{"orders":[{"orderId":"O1","figi":"AAPL.US","quantity":1,"price":180,"status":"FILLED","orderType":"LIMIT"}]}'),
        ]);
        $manager = new FreedomManager(new FreedomClientFactory(
            new FreedomConfig('https://api.test', FreedomHttpClient::V2),
            http: new Client(['handler' => HandlerStack::create($mock)]),
        ));

        $orders = $manager->connect('public-key', 'private-key')
            ->orderHistory('ACC-001', '2025-01-01', '2025-01-31');

        parse_str((string)$mock->getLastRequest()->getBody(), $form);

        $this->assertSame('get-orders-history', $form['cmd']);
        $this->assertSame('ACC-001', $form['params']['accountId']);
        $this->assertSame('O1', $orders[0]->orderId);
        $this->assertSame('AAPL.US', $orders[0]->figi);
    }
}
