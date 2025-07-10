<?php

namespace MasyaSmv\FreedomBrokerApi\DTO;

final class BalanceDTO
{
    /**
     * @param string $currency код валюты, например "USD"
     * @param float $amount сумма баланса в этой валюте
     * @param array<string,mixed> $raw весь «сырый» фрагмент из API
     */
    public function __construct(
        public string $currency,
        public float $amount,
        public array $raw = []
    ) {
    }
}

