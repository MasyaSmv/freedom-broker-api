<?php

namespace MasyaSmv\FreedomBrokerApi\DTO;

final class TickerSearchResultDTO
{
    /**
     * @param array<string, mixed> $raw
     */
    public function __construct(
        public int $instrumentId,
        public string $ticker,
        public string $name,
        public string $longName,
        public string $englishName,
        public string $isin,
        public int $type,
        public int $kind,
        public string $market,
        public int $marketId,
        public string $exchangeCode,
        public array $raw = [],
    ) {
    }
}
