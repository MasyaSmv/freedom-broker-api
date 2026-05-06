<?php

namespace MasyaSmv\FreedomBrokerApi\DTO;

final class AllowedTickerDTO
{
    /**
     * @param int[] $allowedExpires
     * @param array<int, mixed> $recommendations
     * @param array<string, mixed> $raw
     */
    public function __construct(
        public bool $allowed,
        public array $allowedExpires,
        public ?string $restriction,
        public ?string $operation,
        public array $recommendations,
        public array $raw = [],
    ) {
    }
}
