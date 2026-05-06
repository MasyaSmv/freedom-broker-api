<?php

namespace MasyaSmv\FreedomBrokerApi\DTO;

final class TradeHistoryDTO
{
    /**
     * @param array<string, mixed> $raw
     */
    public function __construct(
        public string $id,
        public string $orderId,
        public string $ticker,
        public float $price,
        public float $quantity,
        public float $value,
        public string $date,
        public string $currency,
        public int $type,
        public array $raw = [],
    ) {
    }
}
