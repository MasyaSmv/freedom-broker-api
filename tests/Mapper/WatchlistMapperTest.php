<?php

namespace MasyaSmv\FreedomBrokerApi\Tests\Mapper;

use MasyaSmv\FreedomBrokerApi\Core\Mapper\WatchlistMapper;
use MasyaSmv\FreedomBrokerApi\Core\Response\ApiMeta;
use MasyaSmv\FreedomBrokerApi\Core\Response\ApiPayload;
use MasyaSmv\FreedomBrokerApi\Core\Response\ApiRequestContext;
use MasyaSmv\FreedomBrokerApi\Core\Response\ApiResponse;
use PHPUnit\Framework\TestCase;

final class WatchlistMapperTest extends TestCase
{
    public function test_it_maps_user_stock_lists_payload_to_dtos(): void
    {
        $watchlists = (new WatchlistMapper())->mapResponse($this->response([
            'userStockLists' => [
                [
                    'id' => 1,
                    'userId' => 123456,
                    'name' => 'default',
                    'tickers' => ['FRHC.US', 'CRH.US'],
                    'picture' => null,
                ],
                [
                    'id' => 2,
                    'userId' => 123456,
                    'name' => 'etf',
                    'tickers' => ['AAAU.US'],
                    'picture' => ':)',
                ],
            ],
            'selectedId' => 1,
            'defaultId' => 1,
        ]));

        $this->assertCount(2, $watchlists);
        $this->assertSame(1, $watchlists[0]->id);
        $this->assertSame('default', $watchlists[0]->name);
        $this->assertSame(['FRHC.US', 'CRH.US'], $watchlists[0]->tickers);
        $this->assertTrue($watchlists[0]->selected);
        $this->assertTrue($watchlists[0]->default);
        $this->assertSame(':)', $watchlists[1]->picture);
        $this->assertFalse($watchlists[1]->selected);
    }

    public function test_it_returns_empty_collection_for_unexpected_payload(): void
    {
        $this->assertCount(0, (new WatchlistMapper())->mapPayload(['userStockLists' => null]));
    }

    private function response(array $payload): ApiResponse
    {
        return new ApiResponse(
            new ApiMeta(200, [], 1),
            new ApiRequestContext('getUserStockLists', 'https://api.test/v2/cmd/getUserStockLists', 'POST', 2, []),
            new ApiPayload($payload),
            null,
            json_encode($payload) ?: '',
        );
    }
}
