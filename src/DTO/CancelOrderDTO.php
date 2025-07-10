<?php

namespace MasyaSmv\FreedomBrokerApi\DTO;

final class CancelOrderDTO
{
    public function __construct(
        public string $orderId,
        public string $status,
        public array  $raw = [],
    ) {
    }
}
