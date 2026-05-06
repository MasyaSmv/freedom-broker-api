<?php

namespace MasyaSmv\FreedomBrokerApi\Core\Mapper;

use MasyaSmv\FreedomBrokerApi\Core\Response\ApiResponse;
use MasyaSmv\FreedomBrokerApi\DTO\CancelledOrderDTO;
use MasyaSmv\FreedomBrokerApi\DTO\PlacedOrderDTO;
use MasyaSmv\FreedomBrokerApi\DTO\StopLossResultDTO;

final class OrderWriteMapper
{
    public function mapPlaced(ApiResponse $response): PlacedOrderDTO
    {
        $data = $response->payloadOrFail()->data();

        return new PlacedOrderDTO(
            orderId: (int)($data['order_id'] ?? 0),
            raw: $data,
        );
    }

    public function mapCancelled(ApiResponse $response): CancelledOrderDTO
    {
        $data = $response->payloadOrFail()->data();

        return new CancelledOrderDTO(
            orderId: (int)($data['order_id'] ?? 0),
            raw: $data,
        );
    }

    public function mapStopLoss(ApiResponse $response): StopLossResultDTO
    {
        $data = $response->payloadOrFail()->data();

        /** @var array<string, mixed> $order */
        $order = is_array($data['order'] ?? null) ? $data['order'] : [];

        return new StopLossResultDTO(
            orderId: (int)($data['order_id'] ?? 0),
            order: $order,
            raw: $data,
        );
    }
}
