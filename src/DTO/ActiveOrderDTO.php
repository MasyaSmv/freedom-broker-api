<?php

namespace MasyaSmv\FreedomBrokerApi\DTO;

final class ActiveOrderDTO
{
    public function __construct(
        public int    $orderId,
        public string $ticker,
        public string $name,
        public float  $price,
        public float  $quantity,
        public float  $leavesQty,
        public int    $status,
        public int    $type,
        public int    $operation,
        public string $currency,
        public string $date,
        public array  $raw = [],
    ) {
    }
}
