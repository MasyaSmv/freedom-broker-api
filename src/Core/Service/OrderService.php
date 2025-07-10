<?php

namespace MasyaSmv\FreedomBrokerApi\Core\Service;

use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Collection;
use MasyaSmv\FreedomBrokerApi\Core\Http\FreedomHttpClient;
use MasyaSmv\FreedomBrokerApi\DTO\{CancelOrderDTO, OrderDTO};

final class OrderService
{
    public function __construct(private FreedomHttpClient $client)
    {
    }

    /**
     * Отправить новый приказ на покупку/продажу.
     *
     * @param string $figi
     * @param float $quantity
     * @param float $price
     * @param string $accountId
     *
     * @return OrderDTO
     * @throws GuzzleException
     */
    public function send(string $figi, float $quantity, float $price, string $accountId): OrderDTO
    {
        $payload = [
            'figi' => $figi,
            'quantity' => $quantity,
            'price' => $price,
            'accountId' => $accountId,
        ];

        $resp = $this->client->request(
            'orders-send',
            $payload,
            asArray: true,
        );

        return new OrderDTO(
            orderId: (string)($resp['orderId'] ?? ''),
            figi: $resp['figi'] ?? $figi,
            quantity: (float)($resp['quantity'] ?? $quantity),
            price: (float)($resp['price'] ?? $price),
            status: $resp['status'] ?? '',
            type: $resp['orderType'] ?? null,
            raw: $resp,
        );
    }

    /**
     * Отменить существующий приказ.
     *
     * @param string $orderId
     *
     * @return CancelOrderDTO
     * @throws GuzzleException
     */
    public function cancel(string $orderId): CancelOrderDTO
    {
        $resp = $this->client->request(
            'orders-delete',
            ['orderId' => $orderId],
            asArray: true,
        );

        return new CancelOrderDTO(
            orderId: (string)($resp['orderId'] ?? $orderId),
            status: $resp['status'] ?? '',
            raw: $resp,
        );
    }

    /**
     * Получить историю приказов за период.
     *
     * @return Collection<OrderDTO>
     * @throws GuzzleException
     */
    public function history(
        string $accountId,
        string $from,
        string $to
    ): Collection {
        $resp = $this->client->request(
            'get-orders-history',
            ['accountId' => $accountId, 'from' => $from, 'to' => $to],
            asArray: true,
        );

        $orders = $resp['orders'] ?? [];

        return collect($orders)
            ->map(fn (array $o) => new OrderDTO(
                orderId: (string)($o['orderId'] ?? ''),
                figi: $o['figi'] ?? '',
                quantity: (float)($o['quantity'] ?? 0),
                price: (float)($o['price'] ?? 0),
                status: $o['status'] ?? '',
                type: $o['orderType'] ?? null,
                raw: $o,
            ));
    }
}
