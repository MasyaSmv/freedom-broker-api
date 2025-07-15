<?php

namespace MasyaSmv\FreedomBrokerApi\DTO;

use Carbon\Carbon;

final class ReportPeriodDTO
{
    public function __construct(
        public Carbon $start,
        public Carbon $end,
    ) {
    }

    public function contains(Carbon $date): bool
    {
        return $date->between($this->start, $this->end);
    }

    public function lengthInDays(): int
    {
        return $this->start->diffInDays($this->end) + 1;
    }
}
