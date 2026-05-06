<?php

namespace MasyaSmv\FreedomBrokerApi\Tests\Request;

use InvalidArgumentException;
use MasyaSmv\FreedomBrokerApi\Core\Request\AddWatchlistRequest;
use MasyaSmv\FreedomBrokerApi\Core\Request\AddWatchlistTickerRequest;
use MasyaSmv\FreedomBrokerApi\Core\Request\DeleteWatchlistRequest;
use MasyaSmv\FreedomBrokerApi\Core\Request\DeleteWatchlistTickerRequest;
use MasyaSmv\FreedomBrokerApi\Core\Request\SelectWatchlistRequest;
use MasyaSmv\FreedomBrokerApi\Core\Request\UpdateWatchlistRequest;
use PHPUnit\Framework\TestCase;

final class WatchlistOperationsRequestTest extends TestCase
{
    public function test_add_watchlist_request_builds_payload(): void
    {
        $request = new AddWatchlistRequest('etf', ':)', ['AAAU.US', 'ACES.US']);

        $this->assertSame('addStockList', $request->command());
        $this->assertSame([
            'name' => 'etf',
            'picture' => ':)',
            'tickers' => ['AAAU.US', 'ACES.US'],
        ], $request->toPayload());
    }

    public function test_update_watchlist_request_builds_payload(): void
    {
        $request = new UpdateWatchlistRequest(2, 'etf2', '', 0);

        $this->assertSame('updateStockList', $request->command());
        $this->assertSame(['id' => 2, 'name' => 'etf2', 'picture' => '', 'index' => 0], $request->toPayload());
    }

    public function test_delete_watchlist_request_builds_payload(): void
    {
        $request = new DeleteWatchlistRequest(3);

        $this->assertSame('deleteStockList', $request->command());
        $this->assertSame(['id' => 3], $request->toPayload());
    }

    public function test_add_watchlist_ticker_request_builds_payload(): void
    {
        $request = new AddWatchlistTickerRequest(2, 'AAPL.US', 2);

        $this->assertSame('addStockListTicker', $request->command());
        $this->assertSame(['id' => 2, 'ticker' => 'AAPL.US', 'index' => 2], $request->toPayload());
    }

    public function test_delete_watchlist_ticker_request_builds_payload(): void
    {
        $request = new DeleteWatchlistTickerRequest(2, 'AAPL.US');

        $this->assertSame('deleteStockListTicker', $request->command());
        $this->assertSame(['id' => 2, 'ticker' => 'AAPL.US'], $request->toPayload());
    }

    public function test_select_watchlist_request_builds_payload(): void
    {
        $request = new SelectWatchlistRequest(2);

        $this->assertSame('makeStockListSelected', $request->command());
        $this->assertSame(['id' => 2], $request->toPayload());
    }

    public function test_watchlist_requests_require_positive_id(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new DeleteWatchlistRequest(0);
    }

    public function test_watchlist_requests_require_name(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new AddWatchlistRequest('');
    }

    public function test_watchlist_ticker_requests_require_ticker(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new DeleteWatchlistTickerRequest(1, '');
    }
}
