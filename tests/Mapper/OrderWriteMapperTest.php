<?php

namespace MasyaSmv\FreedomBrokerApi\Tests\Mapper;

use MasyaSmv\FreedomBrokerApi\Core\Mapper\OrderWriteMapper;
use MasyaSmv\FreedomBrokerApi\Core\Response\ApiMeta;
use MasyaSmv\FreedomBrokerApi\Core\Response\ApiPayload;
use MasyaSmv\FreedomBrokerApi\Core\Response\ApiRequestContext;
use MasyaSmv\FreedomBrokerApi\Core\Response\ApiResponse;
use PHPUnit\Framework\TestCase;

final class OrderWriteMapperTest extends TestCase
{
    public function test_map_placed_extracts_order_id(): void
    {
        $dto = (new OrderWriteMapper())->mapPlaced(
            $this->response(['order_id' => 4982349829328]),
        );

        $this->assertSame(4982349829328, $dto->orderId);
    }

    public function test_map_cancelled_extracts_order_id(): void
    {
        $dto = (new OrderWriteMapper())->mapCancelled(
            $this->response(['order_id' => 2929292929]),
        );

        $this->assertSame(2929292929, $dto->orderId);
    }

    public function test_map_stop_loss_extracts_order_id_and_order(): void
    {
        $orderData = [
            'id'       => 192054709,
            'order_id' => 192054709,
            'instr'    => 'SIE.EU',
            'stat'     => 10,
            'type'     => 6,
        ];

        $dto = (new OrderWriteMapper())->mapStopLoss(
            $this->response(['order_id' => 192054709, 'order' => $orderData]),
        );

        $this->assertSame(192054709, $dto->orderId);
        $this->assertSame('SIE.EU', $dto->order['instr']);
        $this->assertSame(10, $dto->order['stat']);
    }

    public function test_map_stop_loss_missing_order_falls_back_to_empty(): void
    {
        $dto = (new OrderWriteMapper())->mapStopLoss(
            $this->response(['order_id' => 1]),
        );

        $this->assertSame([], $dto->order);
    }

    /**
     * @param array<string, mixed> $data
     */
    private function response(array $data): ApiResponse
    {
        return new ApiResponse(
            new ApiMeta(200, [], 1),
            new ApiRequestContext('putTradeOrder', 'https://api.test/v2/cmd/putTradeOrder', 'POST', 2, []),
            new ApiPayload($data),
            null,
            json_encode($data) ?: '',
        );
    }
}
