<?php

namespace MasyaSmv\FreedomBrokerApi\DTO;

final class StockQuoteDTO
{
    /**
     * @param array<string, mixed> $raw
     */
    public function __construct(
        public string $ticker,
        public string $name,
        public string $name2,
        public ?float $lastPrice,
        public ?float $previousClose,
        public ?float $change,
        public ?float $percentChange,
        public ?float $bestBid,
        public ?float $bestAsk,
        public ?int $volume,
        public ?string $lastTradeAt,
        public array $raw = [],
    ) {
    }
}
