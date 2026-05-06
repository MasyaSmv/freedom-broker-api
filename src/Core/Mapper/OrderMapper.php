<?php

namespace MasyaSmv\FreedomBrokerApi\Core\Mapper;

use Illuminate\Support\Collection;
use MasyaSmv\FreedomBrokerApi\Core\Response\ApiResponse;
use MasyaSmv\FreedomBrokerApi\DTO\ActiveOrderDTO;
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
     * @param array<int|string, mixed> $payload
     * @return Collection<int, OrderDTO>
     */
    public function mapHistoryPayload(array $payload): Collection
    {
        $orders = $payload['orders'] ?? [];
        $result = collect();

        // Real API response shape: { "orders": { "key": "USER", "order": [...] } }
        if (is_array($orders) && array_key_exists('order', $orders)) {
            $rows = $orders['order'] ?? [];
        } elseif (is_array($orders) && !array_key_exists('order', $orders)) {
            // Flat array fallback (legacy/test data)
            $rows = $orders;
        } else {
            return $result;
        }

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
     * @return Collection<int, ActiveOrderDTO>
     */
    public function mapCurrentOrdersResponse(ApiResponse $response): Collection
    {
        return $this->mapCurrentOrdersPayload($response->payloadOrFail()->data());
    }

    /**
     * @param array<string, mixed> $payload
     * @return Collection<int, ActiveOrderDTO>
     */
    public function mapCurrentOrdersPayload(array $payload): Collection
    {
        $result = collect();

        // getNotifyOrderJson returns a top-level array of orders, not a wrapped object.
        $rows = array_values($payload);
        if (isset($rows[0]) && is_array($rows[0]) && isset($rows[0]['order_id'])) {
            foreach ($rows as $row) {
                if (is_array($row)) {
                    $result->push($this->mapActiveOrderRow($row));
                }
            }
        }

        return $result;
    }

    /**
     * @param array<string, mixed> $row
     */
    public function mapActiveOrderRow(array $row): ActiveOrderDTO
    {
        return new ActiveOrderDTO(
            orderId: (int)($row['order_id'] ?? 0),
            ticker: (string)($row['instr'] ?? ''),
            name: (string)($row['name'] ?? ''),
            price: (float)($row['p'] ?? 0),
            quantity: (float)($row['q'] ?? 0),
            leavesQty: (float)($row['leaves_qty'] ?? 0),
            status: (int)($row['stat'] ?? 0),
            type: (int)($row['type'] ?? 0),
            operation: (int)($row['oper'] ?? 0),
            currency: (string)($row['cur'] ?? ''),
            date: (string)($row['date'] ?? ''),
            raw: $row,
        );
    }

    /**
     * @param array<string, mixed> $row
     */
    public function mapRow(array $row): OrderDTO
    {
        return new OrderDTO(
            orderId: (string)($row['id'] ?? $row['orderId'] ?? $row['order_id'] ?? ''),
            figi: (string)($row['instr'] ?? $row['figi'] ?? $row['ticker'] ?? $row['symbol'] ?? ''),
            quantity: (float)($row['q'] ?? $row['quantity'] ?? $row['qty'] ?? 0),
            price: (float)($row['p'] ?? $row['price'] ?? 0),
            status: (string)($row['stat'] ?? $row['status'] ?? ''),
            type: isset($row['type']) || isset($row['orderType'])
                ? (string)($row['type'] ?? $row['orderType'])
                : null,
            raw: $row,
        );
    }
}
