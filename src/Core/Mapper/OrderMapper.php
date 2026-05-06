<?php

namespace MasyaSmv\FreedomBrokerApi\Core\Mapper;

use Illuminate\Support\Collection;
use MasyaSmv\FreedomBrokerApi\Core\Response\ApiResponse;
use MasyaSmv\FreedomBrokerApi\DTO\OrderDTO;

final class OrderMapper
{
    /**
     * @return Collection<int, OrderDTO>
     */
    public function mapHistoryResponse(ApiResponse $response): Collection
    {
        return $this->mapHistoryPayload($response->payloadOrFail()->data());
    }

    /**
     * @param array<string, mixed> $payload
     * @return Collection<int, OrderDTO>
     */
    public function mapHistoryPayload(array $payload): Collection
    {
        $rows = $payload['orders'] ?? [];
        $result = collect();

        if (!is_array($rows)) {
            return $result;
        }

        foreach ($rows as $row) {
            if (is_array($row)) {
                $result->push($this->mapRow($row));
            }
        }

        return $result;
    }

    /**
     * @param array<string, mixed> $row
     */
    public function mapRow(array $row): OrderDTO
    {
        return new OrderDTO(
            orderId: (string)($row['orderId'] ?? $row['order_id'] ?? $row['id'] ?? ''),
            figi: (string)($row['figi'] ?? $row['ticker'] ?? $row['symbol'] ?? ''),
            quantity: (float)($row['quantity'] ?? $row['qty'] ?? 0),
            price: (float)($row['price'] ?? 0),
            status: (string)($row['status'] ?? ''),
            type: isset($row['orderType']) || isset($row['type'])
                ? (string)($row['orderType'] ?? $row['type'])
                : null,
            raw: $row,
        );
    }
}
