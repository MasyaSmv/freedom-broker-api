<?php

namespace MasyaSmv\FreedomBrokerApi\DTO;

final class OrderDTO
{
    public function __construct(
        public string $orderId,
        public string $figi,
        public float  $quantity,
        public float  $price,
        public string $status,
        public ?string $type    = null,
        public array   $raw     = [],
    ) {
    }
}
