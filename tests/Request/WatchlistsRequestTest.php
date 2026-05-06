<?php

namespace MasyaSmv\FreedomBrokerApi\Tests\Request;

use MasyaSmv\FreedomBrokerApi\Core\Request\WatchlistsRequest;
use PHPUnit\Framework\TestCase;

final class WatchlistsRequestTest extends TestCase
{
    public function test_it_builds_get_user_stock_lists_command(): void
    {
        $request = new WatchlistsRequest();

        $this->assertSame('getUserStockLists', $request->command());
        $this->assertSame([], $request->toPayload());
    }
}
