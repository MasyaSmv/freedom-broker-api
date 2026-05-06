<?php

namespace MasyaSmv\FreedomBrokerApi\DTO;

final class PortfolioPositionDTO
{
    public function __construct(
        public string  $ticker,
        public float   $quantity,
        public string  $currency,
        public float   $currencyRate,
        public string  $name,
        public string  $name2,
        public float   $marketPrice,
        public float   $marketValue,
        public float   $openBalance,
        public float   $profitClose,
        public float   $profitPrice,
        public float   $closePrice,
        public array   $raw = [],
    ) {
    }
}
