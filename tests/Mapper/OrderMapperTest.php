<?php

namespace MasyaSmv\FreedomBrokerApi\Tests\Mapper;

use MasyaSmv\FreedomBrokerApi\Core\Mapper\OrderMapper;
use MasyaSmv\FreedomBrokerApi\Core\Response\ApiMeta;
use MasyaSmv\FreedomBrokerApi\Core\Response\ApiPayload;
use MasyaSmv\FreedomBrokerApi\Core\Response\ApiRequestContext;
use MasyaSmv\FreedomBrokerApi\Core\Response\ApiResponse;
use MasyaSmv\FreedomBrokerApi\DTO\OrderDTO;
use PHPUnit\Framework\TestCase;

final class OrderMapperTest extends TestCase
{
    public function test_it_maps_order_history_payload_to_order_dtos(): void
    {
        $orders = [
            [
                'orderId' => 'O1',
                'figi' => 'AAPL.US',
                'quantity' => 3,
                'price' => 180.25,
                'status' => 'FILLED',
                'orderType' => 'LIMIT',
            ],
            [
                'order_id' => 'O2',
                'ticker' => 'MSFT.US',
                'qty' => 2,
                'price' => 300,
                'status' => 'NEW',
                'type' => 'MARKET',
            ],
        ];

        $result = (new OrderMapper())->mapHistoryResponse($this->response(['orders' => $orders]));

        $this->assertCount(2, $result);
        $this->assertContainsOnlyInstancesOf(OrderDTO::class, $result);
        $this->assertSame('O1', $result[0]->orderId);
        $this->assertSame('AAPL.US', $result[0]->figi);
        $this->assertSame(3.0, $result[0]->quantity);
        $this->assertSame(180.25, $result[0]->price);
        $this->assertSame('FILLED', $result[0]->status);
        $this->assertSame('LIMIT', $result[0]->type);
        $this->assertSame('O2', $result[1]->orderId);
        $this->assertSame('MSFT.US', $result[1]->figi);
        $this->assertSame(2.0, $result[1]->quantity);
        $this->assertSame('MARKET', $result[1]->type);
    }

    public function test_it_returns_empty_collection_for_non_array_orders(): void
    {
        $result = (new OrderMapper())->mapHistoryPayload(['orders' => 'bad-shape']);

        $this->assertCount(0, $result);
    }

    private function response(array $payload): ApiResponse
    {
        return new ApiResponse(
            new ApiMeta(200, [], 1),
            new ApiRequestContext('get-orders-history', 'https://api.test/v2/cmd/get-orders-history', 'POST', 2, []),
            new ApiPayload($payload),
            null,
            json_encode($payload) ?: '',
        );
    }
}
