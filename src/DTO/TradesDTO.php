<?php

namespace MasyaSmv\FreedomBrokerApi\DTO;

final class TradesDTO
{
    /**
     * @param array<int, mixed> $series
     * @param array<string, mixed> $info
     * @param array<string, mixed> $raw
     */
    public function __construct(
        public string $ticker,
        public array $series,
        public array $info,
        public array $raw = [],
    ) {
    }
}
