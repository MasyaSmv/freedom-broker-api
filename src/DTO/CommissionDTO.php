<?php

namespace MasyaSmv\FreedomBrokerApi\DTO;

final class CommissionDTO
{
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
}
