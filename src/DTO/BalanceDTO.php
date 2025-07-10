<?php

namespace MasyaSmv\FreedomBrokerApi\DTO;

final class BalanceDTO
{
    public function __construct(
        public string $currency,
        public float  $amount,
    ) {}
}

