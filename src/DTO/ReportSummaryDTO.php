<?php

namespace MasyaSmv\FreedomBrokerApi\DTO;

class ReportSummaryDTO
{
    /**
     * @param array<string,int> $securities // количество по каждому инструменту
     * @param array<string,float> $total // сумма текущих сделок по валютам
     * @param array<string,float> $prtotal // стоимость портфеля на конец периода по валютам
     * @param array<string,mixed> $raw // всё «сырое» из trades
     */
    public function __construct(
        public array $securities,
        public array $total,
        public array $prtotal,
        public array $raw = [],
    ) {
    }
}
