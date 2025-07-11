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
        return array_merge($defaults, $processed);
    }
}

