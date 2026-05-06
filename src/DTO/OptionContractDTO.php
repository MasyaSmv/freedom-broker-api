<?php

namespace MasyaSmv\FreedomBrokerApi\DTO;

final class OptionContractDTO
{
    /**
     * @param array<string, mixed> $raw
     */
    public function __construct(
        public string $ticker,
        public string $baseContractCode,
        public string $lastTradeDate,
        public string $expireDate,
        public float $strikePrice,
        public string $optionType,
        public array $raw = [],
    ) {
    }
}
