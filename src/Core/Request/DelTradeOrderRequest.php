<?php

namespace MasyaSmv\FreedomBrokerApi\Core\Request;

use MasyaSmv\FreedomBrokerApi\Core\Request\CommandRequestInterface;

final class DelTradeOrderRequest implements CommandRequestInterface
{
    public function __construct(private int $orderId)
    {
    }

    public function command(): string
    {
        return 'delTradeOrder';
    }

    /**
     * @return array<string, mixed>
     */
    public function toPayload(): array
    {
        return ['order_id' => $this->orderId];
    }
}
