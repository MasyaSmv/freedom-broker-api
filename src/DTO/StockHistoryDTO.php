<?php

namespace MasyaSmv\FreedomBrokerApi\DTO;

use DateTimeInterface;

final class StockHistoryDTO
{
    public function __construct(
        public string             $ticker,
        public DateTimeInterface $date,
        public float              $open,
        public float              $high,
        public float              $low,
        public float              $close,
    ) {
    }
}
