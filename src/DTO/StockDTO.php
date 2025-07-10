<?php

namespace MasyaSmv\FreedomBrokerApi\DTO;

use DateTimeInterface;

final class StockDTO
{
    public function __construct(
        public string $ticker,
        public int $freedomId,
        public string $name,
        public string $instrType,
        public ?string $currency,
        public ?float $lastPrice,
        public ?DateTimeInterface $lastTradeAt,
        public array $raw = [],      // для проекта-потребителя
    ) {
    }
}
