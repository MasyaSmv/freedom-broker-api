<?php

namespace MasyaSmv\FreedomBrokerApi\DTO;

final class WatchlistDTO
{
    /**
     * @param string[] $tickers
     * @param array<string, mixed> $raw
     */
    public function __construct(
        public int $id,
        public int $userId,
        public string $name,
        public array $tickers,
        public ?string $picture = null,
        public bool $selected = false,
        public bool $default = false,
        public array $raw = [],
    ) {
    }
}
