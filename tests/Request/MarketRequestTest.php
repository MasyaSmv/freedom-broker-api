<?php

namespace MasyaSmv\FreedomBrokerApi\Tests\Request;

use DateTimeImmutable;
use InvalidArgumentException;
use MasyaSmv\FreedomBrokerApi\Core\Request\AllowedTickerRequest;
use MasyaSmv\FreedomBrokerApi\Core\Request\MarketStatusRequest;
use MasyaSmv\FreedomBrokerApi\Core\Request\OptionsByMarketRequest;
use MasyaSmv\FreedomBrokerApi\Core\Request\QuoteHistoryRequest;
use MasyaSmv\FreedomBrokerApi\Core\Request\QuoteInfoRequest;
use MasyaSmv\FreedomBrokerApi\Core\Request\StockQuotesRequest;
use MasyaSmv\FreedomBrokerApi\Core\Request\TickerFinderRequest;
use MasyaSmv\FreedomBrokerApi\Core\Request\TopSecuritiesRequest;
use MasyaSmv\FreedomBrokerApi\Core\Request\TradesHistoryRequest;
use MasyaSmv\FreedomBrokerApi\Core\Request\TradesRequest;
use PHPUnit\Framework\TestCase;

final class MarketRequestTest extends TestCase
{
    public function test_quote_info_request_requires_ticker(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new QuoteInfoRequest('');
    }

    public function test_quote_history_request_builds_payload(): void
    {
        $payload = (new QuoteHistoryRequest(
            'ABC.US',
            new DateTimeImmutable('2024-01-01'),
            new DateTimeImmutable('2024-01-31'),
        ))->toPayload();

        $this->assertSame('ABC.US', $payload['id']);
        $this->assertSame(1440, $payload['timeframe']);
        $this->assertSame('01.01.2024 00:00', $payload['date_from']);
        $this->assertSame('31.01.2024 00:00', $payload['date_to']);
    }

    public function test_market_status_request_builds_payload(): void
    {
        $request = new MarketStatusRequest('KASE', 'demo');

        $this->assertSame('getMarketStatus', $request->command());
        $this->assertSame(['market' => 'KASE', 'mode' => 'demo'], $request->toPayload());
    }

    public function test_market_status_request_defaults_to_all_markets(): void
    {
        $this->assertSame(['market' => '*'], (new MarketStatusRequest())->toPayload());
    }

    public function test_market_status_request_requires_market(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new MarketStatusRequest('');
    }

    public function test_stock_quotes_request_builds_payload(): void
    {
        $request = new StockQuotesRequest(['AAPL.US', 'F.US']);

        $this->assertSame('getStockQuotesJson', $request->command());
        $this->assertSame(['tickers' => ['AAPL.US', 'F.US']], $request->toPayload());
    }

    public function test_stock_quotes_request_requires_tickers(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new StockQuotesRequest([]);
    }

    public function test_ticker_finder_request_builds_payload(): void
    {
        $request = new TickerFinderRequest('AAPL.US');

        $this->assertSame('tickerFinder', $request->command());
        $this->assertSame(['text' => 'AAPL.US'], $request->toPayload());
    }

    public function test_ticker_finder_request_requires_text(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new TickerFinderRequest('');
    }

    public function test_trades_request_builds_get_hloc_trades_payload(): void
    {
        $request = new TradesRequest('AAPL.US');

        $this->assertSame('getHloc', $request->command());
        $this->assertSame(['id' => 'AAPL.US', 'timeframe' => -1], $request->toPayload());
    }

    public function test_trades_request_requires_ticker(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new TradesRequest('');
    }

    public function test_trades_history_request_builds_payload(): void
    {
        $request = new TradesHistoryRequest(
            '2020-03-23',
            '2020-04-08',
            tradeId: 232327727,
            max: 100,
            ticker: 'AAPL.US',
            currency: 'USD',
            reception: 1,
        );

        $this->assertSame('getTradesHistory', $request->command());
        $this->assertSame([
            'beginDate' => '2020-03-23',
            'endDate' => '2020-04-08',
            'tradeId' => 232327727,
            'max' => 100,
            'nt_ticker' => 'AAPL.US',
            'curr' => 'USD',
            'reception' => 1,
        ], $request->toPayload());
    }

    public function test_trades_history_request_requires_period(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new TradesHistoryRequest('', '2020-04-08');
    }

    public function test_top_securities_request_builds_payload(): void
    {
        $request = new TopSecuritiesRequest('stocks', 'russia', 1, 20);

        $this->assertSame('getTopSecurities', $request->command());
        $this->assertSame([
            'type' => 'stocks',
            'exchange' => 'russia',
            'gainers' => 1,
            'limit' => 20,
        ], $request->toPayload());
    }

    public function test_allowed_ticker_request_builds_payload(): void
    {
        $request = new AllowedTickerRequest('AAPL.US', true);

        $this->assertSame('checkAllowedTickerAndBanOnTrade', $request->command());
        $this->assertSame(['ticker' => 'AAPL.US', 'checkBan' => true], $request->toPayload());
    }

    public function test_options_by_market_request_builds_payload(): void
    {
        $request = new OptionsByMarketRequest('FIX', 'T.US');

        $this->assertSame('getOptionsByMktNameAndBaseAsset', $request->command());
        $this->assertSame(['ltr' => 'FIX', 'base_contract_code' => 'T.US'], $request->toPayload());
    }
}
