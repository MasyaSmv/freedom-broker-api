<?php

namespace MasyaSmv\FreedomBrokerApi\DTO;

final class PriceAlertDTO
{
    /**
     * @param array<string, mixed> $raw
     */
    public function __construct(
        public int $id,
        public string $ticker,
        public float $initPrice,
        public string $triggerPrice,
        public string $quoteType,
        public string $notificationType,
        public string $triggerType,
        public int $periodic,
        public int|string $expire,
        public bool $triggered,
        public bool $deleted,
        public array $raw = [],
    ) {
    }
}
