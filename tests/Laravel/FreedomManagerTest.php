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
            new Response(200, [], '{"result":{"markets":{"t":"2020-11-18 19:29:27","m":[{"n":"KASE","n2":"KASE","s":"CLOSE","o":"08:20:00","c":"14:00:00","dt":"-180"}]}}}'),
            new Response(200, [], '{"result":{"q":[{"c":"AAPL.US","name":"Apple Inc.","ltp":"147,42"}]}}'),
            new Response(200, [], '{"found":[{"instr_id":10000007229,"t":"AAPL.US","n":"Apple","nm":"Apple Inc.","ln":"Apple Inc.","isin":"US0378331005","type":1,"kind":1,"mkt":"FIX","mkt_id":30000000001,"code_nm":"AAPL"}]}'),
            new Response(200, [], '{"AAPL.US":{"series":[[1,2,3]],"info":{"id":"AAPL.US","currency":"USD"}},"took":2.759}'),
            new Response(200, [], '{"trades":{"max_trade_id":[{"@text":"40975888"}],"trade":[{"id":2229992229292,"order_id":299998887727,"p":141.4,"q":20,"v":2828,"date":"2019-08-15T10:10:22","instr_nm":"AAPL.US","curr_c":"USD","type":1}]}}'),
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
        $statuses = $session->marketStatus('KASE', 'demo');
        $quotes = $session->stockQuotes(['AAPL.US']);
        $found = $session->tickerFinder('AAPL.US');
        $trades = $session->trades('AAPL.US');
        $historyTrades = $session->tradesHistory('2020-03-23', '2020-04-08', max: 100);

        $this->assertSame('ABC.US', $stocks[0]->ticker);
        $this->assertSame('ABC.US', $stock?->ticker);
        $this->assertSame(10.0, $history[0]->open);
        $this->assertSame(12.0, $history[0]->high);
        $this->assertSame('KASE', $statuses[0]->market);
        $this->assertSame('CLOSE', $statuses[0]->status);
        $this->assertSame('AAPL.US', $quotes[0]->ticker);
        $this->assertSame(147.42, $quotes[0]->lastPrice);
        $this->assertSame('AAPL.US', $found[0]->ticker);
        $this->assertSame('AAPL.US', $trades[0]->ticker);
        $this->assertSame('40975888', $historyTrades['maxTradeId']);
        $this->assertSame('AAPL.US', $historyTrades['items'][0]->ticker);
    }

    public function test_connect_exposes_typed_order_history(): void
    {
        $fixture = json_decode(
            file_get_contents(__DIR__ . '/../fixtures/order_history_fixture.json'),
            true,
        );

        $mock = new MockHandler([new Response(200, [], json_encode($fixture))]);
        $manager = new FreedomManager(new FreedomClientFactory(
            new FreedomConfig('https://api.test', FreedomHttpClient::V2),
            http: new Client(['handler' => HandlerStack::create($mock)]),
        ));

        $orders = $manager->connect('public-key', 'private-key')
            ->orderHistory('2020-03-23T00:00:00', '2020-04-03T23:59:59');

        parse_str((string)$mock->getLastRequest()->getBody(), $form);

        $this->assertSame('getOrdersHistory', $form['cmd']);
        $this->assertSame('2020-03-23T00:00:00', $form['params']['from']);
        $this->assertSame('2020-04-03T23:59:59', $form['params']['till']);
        $this->assertArrayNotHasKey('accountId', $form['params']);
        $this->assertSame('1111222222112', $orders[0]->orderId);
        $this->assertSame('AAPL.US', $orders[0]->figi);
    }
}
