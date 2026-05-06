<?php

namespace MasyaSmv\FreedomBrokerApi\Tests\Request;

use DateTimeImmutable;
use InvalidArgumentException;
use MasyaSmv\FreedomBrokerApi\Core\Request\MarketStatusRequest;
use MasyaSmv\FreedomBrokerApi\Core\Request\QuoteHistoryRequest;
use MasyaSmv\FreedomBrokerApi\Core\Request\QuoteInfoRequest;
use MasyaSmv\FreedomBrokerApi\Core\Request\StockQuotesRequest;
use MasyaSmv\FreedomBrokerApi\Core\Request\TickerFinderRequest;
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
}
