<?php

namespace MasyaSmv\FreedomBrokerApi\DTO;

use Carbon\Carbon;

final class OperationDTO
{
    protected static array $dateKeys = [
        'transaction_date',
        'datetime',
        'date_created',
        'pay_d',
        'date',
        'short_date',
    ];

    /**
     * @param int $id
     * @param string $type
     * @param string $dateTime
     * @param string $ticker
     * @param float $quantity
     * @param float $price
     * @param float|null $commission
     * @param string|null $currency
     * @param array<string,mixed> $raw полный «сырой» массив из API
     */
    public function __construct(
        public int $id,
        public string $type,
        public string $dateTime,
        public string $ticker,
        public float $quantity,
        public float $price,
        public ?float $commission,
        public ?string $currency,
        public array $raw = [],
    ) {
    }

    /**
     * Конвертирует свойство raw (массив) в финальный массив для БД:
     * 1) Парсит и форматирует dateKeys
     * 2) Сливает дефолтные значения и raw, давая приоритет raw
     *
     * @param array<string,mixed> $defaults Начальные значения
     *
     * @return array<string,mixed>
     */
    public function toDbArray(array $defaults = []): array
    {
        $processed = $this->raw;

        foreach (static::$dateKeys as $key) {
            if (!empty($processed[$key])) {
                $processed[$key] = Carbon::parse($processed[$key]);
            }
        }

        return array_merge($defaults, $processed);
    }
}
