<?php

namespace MasyaSmv\FreedomBrokerApi\DTO;

use Carbon\Carbon;

final class CommissionDTO
{
    protected static array $dateKeys = [
        'dateTime',
    ];

    /**
     * @param float $sum Сумма комиссии
     * @param string $currency Валюта комиссии
     * @param string $type Тип комиссии, за что была выплачена
     * @param string $comment Комментарий к комиссии
     * @param string $dateTime Дата выплаты комиссии
     */
    public function __construct(
        public float $sum,
        public string $currency,
        public string $type,
        public string $comment,
        public string $dateTime,
    ) {
    }


    /**
     * Превращает DTO в массив для БД:
     * 1) Парсит и форматирует все ключи из $dateKeys через Carbon
     * 2) Берёт все свойства объекта
     * 3) Сливает базовый массив $data и свойства объекта, давая приоритет свойствам объекта
     *
     * @param array<string,mixed> $data Базовый массив «дефолтных» значений
     *
     * @return array<string,mixed>
     */
    public function toDbArray(array $data = []): array
    {
        $vars = get_object_vars($this);

        foreach (static::$dateKeys as $key) {
            if (!empty($vars[$key])) {
                $vars[$key] = Carbon::parse($vars[$key]);
            }
        }

        return array_merge($data, $vars);
    }
}
