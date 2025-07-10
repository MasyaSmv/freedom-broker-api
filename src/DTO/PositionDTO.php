<?php

namespace MasyaSmv\FreedomBrokerApi\DTO;

final class PositionDTO
{
    public function __construct(
        public string $ticker,
        public float $quantity,
        public float $marketValue,
        public float $averagePrice,
    ) {
    }
}
