<?php

namespace MasyaSmv\FreedomBrokerApi\DTO;

final class CrossRatesDTO
{
    /**
     * @param array<string, float> $rates
     */
    public function __construct(
        public string $baseCurrency,
        public array  $rates,
    ) {
    }

    public function rate(string $currency): ?float
    {
        return $this->rates[$currency] ?? null;
    }
}
