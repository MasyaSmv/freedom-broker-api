<?php

namespace MasyaSmv\FreedomBrokerApi\DTO;

final class PositionDTO
{
    /**
     * @param string $ticker тикер или код инструмента
     * @param float $quantity количество в портфеле
     * @param float $marketValue рыночная стоимость позиции
     * @param float $averagePrice средняя цена входа (или price_a)
     * @param array<string,mixed> $raw весь «сырой» ответ из API
     */
    public function __construct(
        public string $ticker,
        public float $quantity,
        public float $marketValue,
        public float $averagePrice,
        public array $raw = []
    ) {
    }
}
