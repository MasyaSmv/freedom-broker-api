<?php

namespace MasyaSmv\FreedomBrokerApi\DTO;

final class PortfolioAccountDTO
{
    public function __construct(
        public string $currency,
        public float  $currencyRate,
        public float  $freeFunds,
        public float  $forecastIn,
        public float  $forecastOut,
        public array  $raw = [],
    ) {
    }
}
