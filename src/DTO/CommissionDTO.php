<?php

namespace MasyaSmv\FreedomBrokerApi\DTO;

final class CommissionDTO
{
    public function __construct(
        public float $amount,
        public string $currency,
        public string $date,
        public string $comment,
    ) {
    }
}
