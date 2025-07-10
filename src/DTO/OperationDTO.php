<?php

namespace MasyaSmv\FreedomBrokerApi\DTO;

final class OperationDTO
{
    public function __construct(
        public int $id,
        public string $type,
        public string $dateTime,
        public string $ticker,
        public float $quantity,
        public float $price,
        public ?float $commission,
        public ?string $currency,
    ) {
    }
}
