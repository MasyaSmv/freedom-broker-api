<?php

namespace MasyaSmv\FreedomBrokerApi\DTO;

final class OperationDTO
{
    /**
     * @param int        $id
     * @param string     $type
     * @param string     $dateTime
     * @param string     $ticker
     * @param float      $quantity
     * @param float      $price
     * @param float|null $commission
     * @param string|null $currency
     * @param array<string,mixed> $raw полный «сырой» массив из API
     */
    public function __construct(
        public int   $id,
        public string $type,
        public string $dateTime,
        public string $ticker,
        public float  $quantity,
        public float  $price,
        public ?float $commission,
        public ?string $currency,
        public array  $raw = [],       // <-- здесь храним всё остальное
    ) {
    }
}
