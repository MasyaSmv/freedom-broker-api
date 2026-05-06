<?php

namespace MasyaSmv\FreedomBrokerApi\Tests\Api;

use DateTimeImmutable;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use MasyaSmv\FreedomBrokerApi\Config\FreedomHttpConfig;
use MasyaSmv\FreedomBrokerApi\Core\Api\MarketApi;
use MasyaSmv\FreedomBrokerApi\Core\Client\FreedomClient;
use MasyaSmv\FreedomBrokerApi\Core\Http\FreedomHttpClient;
use MasyaSmv\FreedomBrokerApi\Core\Http\Signer\V2Signer;
use MasyaSmv\FreedomBrokerApi\Core\Request\MarketStatusRequest;
use MasyaSmv\FreedomBrokerApi\Core\Request\QuoteHistoryRequest;
use MasyaSmv\FreedomBrokerApi\Core\Request\QuoteInfoRequest;
use MasyaSmv\FreedomBrokerApi\Core\Request\StockQuotesRequest;
use MasyaSmv\FreedomBrokerApi\Core\Request\TickerFinderRequest;
use MasyaSmv\FreedomBrokerApi\Core\Request\TradesHistoryRequest;
use MasyaSmv\FreedomBrokerApi\Core\Request\TradesRequest;
use MasyaSmv\FreedomBrokerApi\Core\Transport\FreedomTransport;
use PHPUnit\Framework\TestCase;

final class MarketApiTest extends TestCase
{
    public function test_it_sends_quote_info_command(): void
    {
        $mock = new MockHandler([new Response(200, [], '{"c":"ABC.US"}')]);
        $response = (new MarketApi($this->client($mock)))->quoteInfo(new QuoteInfoRequest('ABC.US'));

        parse_str((string)$mock->getLastRequest()->getBody(), $form);

        $this->assertSame('getSecurityInfo', $form['cmd']);
        $this->assertSame('ABC.US', $form['params']['ticker']);
        $this->assertSame('ABC.US', $response->payloadOrFail()->get('c'));
    }

    public function test_it_sends_quote_history_command(): void
    {
        $mock = new MockHandler([new Response(200, [], '{"hloc":{"ABC.US":[]}}')]);
        (new MarketApi($this->client($mock)))->quoteHistory(new QuoteHistoryRequest(
            'ABC.US',
            new DateTimeImmutable('2024-01-01'),
            new DateTimeImmutable('2024-01-31'),
        ));

        parse_str((string)$mock->getLastRequest()->getBody(), $form);

        $this->assertSame('getHloc', $form['cmd']);
        $this->assertSame('ABC.US', $form['params']['id']);
        $this->assertSame('01.01.2024 00:00', $form['params']['date_from']);
        $this->assertSame('31.01.2024 00:00', $form['params']['date_to']);
    }

    public function test_it_sends_market_status_command(): void
    {
        $mock = new MockHandler([new Response(200, [], '{"result":{"markets":{"m":[]}}}')]);
        (new MarketApi($this->client($mock)))->marketStatus(new MarketStatusRequest('KASE', 'demo'));

        parse_str((string)$mock->getLastRequest()->getBody(), $form);

        $this->assertSame('getMarketStatus', $form['cmd']);
        $this->assertSame('KASE', $form['params']['market']);
        $this->assertSame('demo', $form['params']['mode']);
    }

    public function test_it_sends_stock_quotes_command(): void
    {
        $mock = new MockHandler([new Response(200, [], '{"result":{"q":[]}}')]);
        (new MarketApi($this->client($mock)))->stockQuotes(new StockQuotesRequest(['AAPL.US', 'F.US']));

        parse_str((string)$mock->getLastRequest()->getBody(), $form);

        $this->assertSame('getStockQuotesJson', $form['cmd']);
        $this->assertSame(['AAPL.US', 'F.US'], $form['params']['tickers']);
    }

    public function test_it_sends_ticker_finder_command(): void
    {
        $mock = new MockHandler([new Response(200, [], '{"found":[]}')]);
        (new MarketApi($this->client($mock)))->tickerFinder(new TickerFinderRequest('AAPL.US'));

        parse_str((string)$mock->getLastRequest()->getBody(), $form);

        $this->assertSame('tickerFinder', $form['cmd']);
        $this->assertSame('AAPL.US', $form['params']['text']);
    }

    public function test_it_sends_trades_command(): void
    {
        $mock = new MockHandler([new Response(200, [], '{"AAPL.US":{"series":[],"info":{}}}')]);
        (new MarketApi($this->client($mock)))->trades(new TradesRequest('AAPL.US'));

        parse_str((string)$mock->getLastRequest()->getBody(), $form);

        $this->assertSame('getHloc', $form['cmd']);
        $this->assertSame('AAPL.US', $form['params']['id']);
        $this->assertSame('-1', $form['params']['timeframe']);
    }

    public function test_it_sends_trades_history_command(): void
    {
        $mock = new MockHandler([new Response(200, [], '{"trades":{"trade":[]}}')]);
        (new MarketApi($this->client($mock)))->tradesHistory(new TradesHistoryRequest(
            '2020-03-23',
            '2020-04-08',
            max: 100,
            ticker: 'AAPL.US',
        ));

        parse_str((string)$mock->getLastRequest()->getBody(), $form);

        $this->assertSame('getTradesHistory', $form['cmd']);
        $this->assertSame('2020-03-23', $form['params']['beginDate']);
        $this->assertSame('2020-04-08', $form['params']['endDate']);
        $this->assertSame('100', $form['params']['max']);
        $this->assertSame('AAPL.US', $form['params']['nt_ticker']);
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
