<?php

namespace MasyaSmv\FreedomBrokerApi\DTO;

final class CashFlowDTO
{
    public function __construct(
        public string $id,
        public string $typeCode,
        public string $date,
        public string $sum,
        public string $currency,
        public string $comment,
        public string $typeCodeName,
        public string $icon = '',
        public array  $raw = [],
    ) {
    }
}
