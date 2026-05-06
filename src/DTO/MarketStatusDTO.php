<?php

namespace MasyaSmv\FreedomBrokerApi\DTO;

final class MarketStatusDTO
{
    /**
     * @param array<string, mixed> $raw
     */
    public function __construct(
        public string $market,
        public string $name,
        public string $status,
        public string $opensAt,
        public ?string $closesAt,
        public ?int $timeOffsetMinutes,
        public ?string $requestedAt,
        public array $raw = [],
    ) {
    }
}
