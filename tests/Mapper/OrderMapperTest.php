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
    public function test_it_maps_real_api_response_shape(): void
    {
        $fixture = json_decode(
            file_get_contents(__DIR__ . '/../fixtures/order_history_fixture.json'),
            true,
        );

        $result = (new OrderMapper())->mapHistoryResponse($this->response($fixture));

        $this->assertCount(2, $result);
        $this->assertContainsOnlyInstancesOf(OrderDTO::class, $result);

        $first = $result[0];
        $this->assertSame('1111222222112', $first->orderId);
        $this->assertSame('AAPL.US', $first->figi);
        $this->assertSame(10.0, $first->quantity);
        $this->assertSame(100.0, $first->price);
        $this->assertSame('31', $first->status);
        $this->assertSame('2', $first->type);

        $second = $result[1];
        $this->assertSame('2222333344445', $second->orderId);
        $this->assertSame('MSFT.US', $second->figi);
        $this->assertSame(5.0, $second->quantity);
        $this->assertSame('1', $second->type);
    }

    public function test_it_returns_empty_collection_for_missing_order_key(): void
    {
        $result = (new OrderMapper())->mapHistoryPayload(['orders' => ['key' => 'USER']]);

        $this->assertCount(0, $result);
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
            new ApiRequestContext('getOrdersHistory', 'https://api.test/v2/cmd/getOrdersHistory', 'POST', 2, []),
            new ApiPayload($payload),
            null,
            json_encode($payload) ?: '',
        );
    }
}
